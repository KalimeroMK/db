<?php

declare(strict_types=1);

namespace Yiisoft\Db\Tests\Db;

use PHPUnit\Framework\TestCase;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Schema\Column\ColumnInterface;
use Yiisoft\Db\Schema\Column\IntegerColumn;

use function Yiisoft\Db\dbTypecast;

/**
 * @group db
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class FunctionsTest extends TestCase
{
    public function testDbTypecastReturnsNullAsIs(): void
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->expects($this->never())->method('dbTypecast');

        $this->assertNull(dbTypecast($column, null));
    }

    public function testDbTypecastReturnsExpressionAsIs(): void
    {
        $expression = new Expression('1');

        // Expressions are passed to the column; core columns return them as is.
        $this->assertSame($expression, dbTypecast(new IntegerColumn(), $expression));
    }

    public function testDbTypecastDelegatesToColumn(): void
    {
        $this->assertSame(1, dbTypecast(new IntegerColumn(), '1'));
    }
}
