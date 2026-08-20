<?php

declare(strict_types=1);

namespace Yiisoft\Db\Schema\Column;

use Yiisoft\Db\Constant\ColumnType;

/**
 * Represents the metadata for a boolean column.
 */
class BooleanColumn extends AbstractColumn
{
    protected const DEFAULT_TYPE = ColumnType::BOOLEAN;

    public function dbTypecast(mixed $value): ?bool
    {
        return match ($value) {
            true => true,
            false => false,
            null, '' => null,
            default => (bool) $value,
        };
    }

    public function phpTypecast(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return $value && $value !== "\0";
    }
}
