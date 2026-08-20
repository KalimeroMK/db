<?php

declare(strict_types=1);

namespace Yiisoft\Db;

use Yiisoft\Db\Schema\Column\ColumnInterface;

/**
 * Converts a PHP value to its database representation using the given column's typecasting.
 *
 * If the value is `null`, it is returned as is, without calling {@see ColumnInterface::dbTypecast()}.
 *
 * Expressions are NOT filtered on purpose: some implementations process them (for example, MSSQL's binary
 * column unwraps `Param` values), so they must reach {@see ColumnInterface::dbTypecast()}.
 */
function dbTypecast(ColumnInterface $column, mixed $value): mixed
{
    if ($value === null) {
        return null;
    }

    return $column->dbTypecast($value);
}
