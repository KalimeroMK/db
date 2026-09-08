<?php

declare(strict_types=1);

namespace Yiisoft\Db\Tests\Db\Expression\Value\Builder;

use PHPUnit\Framework\TestCase;
use Yiisoft\Db\Constant\DataType;
use Yiisoft\Db\Expression\Value\Builder\UuidValueBuilder;
use Yiisoft\Db\Expression\Value\Param;
use Yiisoft\Db\Expression\Value\UuidValue;
use Yiisoft\Db\Helper\DbUuidHelper;
use Yiisoft\Db\Tests\Support\TestHelper;

/**
 * @group db
 */
final class UuidValueBuilderTest extends TestCase
{
    private const UUID = '738146be-87b1-49f2-9913-36142fb6fcbe';

    public function testBuild(): void
    {
        $db = TestHelper::createSqliteMemoryConnection();
        $builder = new UuidValueBuilder($db->getQueryBuilder());

        $params = [];
        $result = $builder->build(new UuidValue(self::UUID), $params);

        $this->assertSame(':qp0', $result);
        $this->assertEquals([':qp0' => new Param(self::UUID, DataType::STRING)], $params);
    }

    public function testBuildAppendsToExistingParams(): void
    {
        $db = TestHelper::createSqliteMemoryConnection();
        $builder = new UuidValueBuilder($db->getQueryBuilder());

        $params = [':qp0' => new Param('existing', DataType::STRING)];
        $result = $builder->build(new UuidValue(self::UUID), $params);

        $this->assertSame(':qp1', $result);
        $this->assertEquals(
            [':qp0' => new Param('existing', DataType::STRING), ':qp1' => new Param(self::UUID, DataType::STRING)],
            $params,
        );
    }

    /**
     * DBMS that store a UUID as raw bytes override {@see UuidValueBuilder::prepareValue()}.
     */
    public function testPrepareValueIsOverridable(): void
    {
        $db = TestHelper::createSqliteMemoryConnection();
        $builder = new class ($db->getQueryBuilder()) extends UuidValueBuilder {
            protected function prepareValue(UuidValue $expression): Param
            {
                return new Param(DbUuidHelper::uuidToBlob($expression->value), DataType::LOB);
            }
        };

        $params = [];
        $result = $builder->build(new UuidValue(self::UUID), $params);

        $this->assertSame(':qp0', $result);
        $this->assertEquals(
            [':qp0' => new Param(DbUuidHelper::uuidToBlob(self::UUID), DataType::LOB)],
            $params,
        );
    }
}
