<?php

declare(strict_types=1);

namespace Yiisoft\Db\Expression\Value\Builder;

use Yiisoft\Db\Expression\ExpressionBuilderInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Expression\Value\UuidValue;
use Yiisoft\Db\QueryBuilder\QueryBuilderInterface;
use Yiisoft\Db\Helper\DbUuidHelper;

/**
 * Builder for {@see UuidValue} expressions.
 *
 * Binds the UUID in the canonical string form, which is what PostgreSQL `uuid` and MSSQL `uniqueidentifier` columns
 * expect. DBMS that store a UUID as raw bytes, such as MySQL, MariaDB, SQLite and Oracle, override
 * {@see prepareValue()} to convert the value with {@see DbUuidHelper::uuidToBlob()}.
 *
 * @implements ExpressionBuilderInterface<UuidValue>
 */
class UuidValueBuilder implements ExpressionBuilderInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder The query builder instance.
     */
    public function __construct(
        protected readonly QueryBuilderInterface $queryBuilder,
    ) {}

    public function build(ExpressionInterface $expression, array &$params = []): string
    {
        return $this->queryBuilder->buildValue($this->prepareValue($expression), $params);
    }

    /**
     * Converts the UUID to the representation expected by the DBMS.
     *
     * @param UuidValue $expression The expression to convert.
     *
     * @return mixed The value to bind, it's passed to {@see QueryBuilderInterface::buildValue()}.
     */
    protected function prepareValue(UuidValue $expression): mixed
    {
        return $expression->value;
    }
}
