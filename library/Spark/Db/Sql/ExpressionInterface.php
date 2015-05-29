<?php

namespace Spark\Db\Sql;

interface ExpressionInterface
{
    /**
     * Returns the SQL expression.
     *
     * @return string the SQL expressions.
     */
    public function getExpression();
}
