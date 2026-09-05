<?php

declare(strict_types=1);

namespace Yiisoft\Db\Tests\Db\Driver\Pdo;

use PHPUnit\Framework\TestCase;
use Yiisoft\Db\Constant\DataType;
use Yiisoft\Db\Tests\Support\TestHelper;

/**
 * @group db
 */
final class PdoCommandTest extends TestCase
{
    public function testBindParamResolvesDataTypeFromValue(): void
    {
        $db = TestHelper::createSqliteMemoryConnection();

        $intCommand = $db->createCommand('SELECT :value');
        $intValue = 42;
        $intCommand->bindParam(':value', $intValue);
        $intStatement = $intCommand->getPdoStatement();

        $this->assertNotNull($intStatement);
        $this->assertTrue($intStatement->execute());
        $this->assertSame(42, $intStatement->fetchColumn());

        $stringCommand = $db->createCommand('SELECT :value');
        $stringValue = '42';
        $stringCommand->bindParam(':value', $stringValue);
        $stringStatement = $stringCommand->getPdoStatement();

        $this->assertNotNull($stringStatement);
        $this->assertTrue($stringStatement->execute());
        $this->assertSame('42', $stringStatement->fetchColumn());

        $db->close();
    }

    public function testBindValueResolvesDataTypeFromValue(): void
    {
        $db = TestHelper::createSqliteMemoryConnection();
        $command = $db->createCommand();

        $command->bindValue(':int', 42);
        $command->bindValue(':string', 'value');
        $command->bindValue(':null', null);
        $command->bindValue(':explicit', 42, DataType::STRING);

        $params = $command->getParams(false);

        $this->assertSame(DataType::INTEGER, $params[':int']->type);
        $this->assertSame(DataType::STRING, $params[':string']->type);
        $this->assertSame(DataType::NULL, $params[':null']->type);
        $this->assertSame(DataType::STRING, $params[':explicit']->type);

        $db->close();
    }
}
