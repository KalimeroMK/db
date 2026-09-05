<?php

declare(strict_types=1);

namespace Yiisoft\Db\Tests\Db\Connection;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Exception\NotSupportedException;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\Query;
use Yiisoft\Db\Tests\Support\Assert;
use Yiisoft\Db\Tests\Support\Stub\StubColumnFactory;
use Yiisoft\Db\Tests\Support\Stub\StubConnection;
use Yiisoft\Db\Tests\Support\Stub\StubPdoDriver;
use Yiisoft\Test\Support\SimpleCache\MemorySimpleCache;

/**
 * @group db
 */
final class ConnectionTest extends TestCase
{
    public function testGetTableSchema(): void
    {
        $db = $this->createConnection();
        $tableSchema = $db->getTableSchema('non_existing_table');
        $db->close();

        $this->assertNull($tableSchema);
    }

    public function testConstructColumnFactory(): void
    {
        $columnFactory = new StubColumnFactory();

        $db = $this->createConnection($columnFactory);

        $this->assertSame($columnFactory, $db->getColumnFactory());
    }

    public function testCreateQuery(): void
    {
        $db = $this->createConnection();

        $this->assertInstanceOf(Query::class, $db->createQuery());
    }

    #[TestWith(['columns' => 'column1'])]
    #[TestWith(['columns' => 'now()'])]
    #[TestWith(['columns' => true])]
    #[TestWith(['columns' => 1])]
    #[TestWith(['columns' => 1.2])]
    #[TestWith(['columns' => new Expression('now()')])]
    #[TestWith(['columns' => ['column1', 'now()', new Expression('now()')]])]
    public function testSelect(array|bool|float|int|string|ExpressionInterface $columns, ?string $option = null): void
    {
        $db = $this->createConnection();

        Assert::objectsEquals($db->select($columns, $option), $db->createQuery()->select($columns, $option));
    }

    public function testSelectWithoutParams(): void
    {
        $db = $this->createConnection();

        Assert::objectsEquals($db->select(), $db->createQuery());
    }

    public function testBeginTransactionCreatesTransactionAndReusesActiveOne(): void
    {
        $db = $this->createConnection();
        $db->setEnableSavepoint(false);

        $transaction = $db->beginTransaction();

        $this->assertTrue($transaction->isActive());
        $this->assertSame(1, $transaction->getLevel());
        $this->assertSame($transaction, $db->getTransaction());

        // A nested call reuses the active transaction instead of creating a new one.
        try {
            $db->beginTransaction();
            $this->fail('Nested transaction is expected to fail when savepoints are disabled.');
        } catch (NotSupportedException $e) {
            $this->assertSame('Transaction not started: nested transaction not supported.', $e->getMessage());
        }

        $this->assertSame($transaction, $db->getTransaction());
        $this->assertSame(1, $transaction->getLevel());

        $transaction->rollBack();

        $this->assertFalse($transaction->isActive());
        $this->assertNull($db->getTransaction());

        $db->close();
    }

    private function createConnection(?StubColumnFactory $columnFactory = null): StubConnection
    {
        return new StubConnection(
            new StubPdoDriver('sqlite::memory:'),
            new SchemaCache(
                new MemorySimpleCache(),
            ),
            $columnFactory,
        );
    }
}
