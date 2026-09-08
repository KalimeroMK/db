<?php

declare(strict_types=1);

namespace Yiisoft\Db\Expression\Value\Builder;

use Yiisoft\Db\Constant\DataType;
use Yiisoft\Db\Expression\ExpressionBuilderInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Expression\Value\Param;
use Yiisoft\Db\Expression\Value\UuidValue;
use Yiisoft\Db\Helper\DbUuidHelper;
use Yiisoft\Db\QueryBuilder\QueryBuilderInterface;

/**
 * Builder for {@see UuidValue} expressions.
 *
 * Binds the UUID as a string parameter in the canonical form, which is what PostgreSQL `uuid` and MSSQL
 * `uniqueidentifier` columns expect. DBMS that store a UUID as raw bytes, such as MySQL, MariaDB, SQLite and Oracle,
 * override {@see prepareValue()} to convert the value with {@see DbUuidHelper::uuidToBlob()} and bind it as
 * {@see DataType::LOB}, so the driver sends it as binary rather than as a character string.
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
     * Converts the UUID to the parameter expected by the DBMS.
     *
     * @param UuidValue $expression The expression to convert.
     *
     * @return Param The parameter to bind, it's passed to {@see QueryBuilderInterface::buildValue()}.
     */
    protected function prepareValue(UuidValue $expression): Param
    {
        return new Param($expression->value, DataType::STRING);
    }
}
