<?php

declare(strict_types=1);

namespace Yiisoft\Db\Schema\Column;

use Yiisoft\Db\Constant\ColumnType;

use function is_int;

/**
 * Represents the metadata for a bit column.
 */
class BitColumn extends AbstractColumn
{
    protected const DEFAULT_TYPE = ColumnType::BIT;

    public function dbTypecast(mixed $value): int|string|null
    {
        if (is_int($value)) {
            return $value;
        }

        return match ($value) {
            null, '' => null,
            default => (int) $value,
        };
    }

    public function phpTypecast(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int) $value;
    }
}
