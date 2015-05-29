<?php

namespace Spark\Db\Query;

interface OrderCapableInterface
{
    /**
     * Specifies how the results should be ordered. Removes if set any previously ordering.
     *
     * @param string $column the column to order by.
     * @param string $sort how to sort the results, only 'ASC' and 'DESC' are allowed.
     */
    public function orderBy($column, $sort = null);
    
    /**
     * Specifies additional ordering to be applied on the query results.
     *
     * @param string $column the column to order by.
     * @param string $sort how to sort the results, only 'ASC' and 'DESC' are allowed.
     */
    public function addOrderBy($column, $sort = null);
}
