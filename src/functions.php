<?php

declare(strict_types=1);

namespace Yiisoft\Db;

use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Schema\Column\ColumnInterface;

/**
 * Converts a PHP value to its database representation using the given column's typecasting.
 *
 * If the value is `null` or an {@see ExpressionInterface}, it is returned as is, without calling
 * {@see ColumnInterface::dbTypecast()}.
 */
function dbTypecast(ColumnInterface $column, mixed $value): mixed
{
    if ($value === null || $value instanceof ExpressionInterface) {
        return $value;
    }

    return $column->dbTypecast($value);
}
