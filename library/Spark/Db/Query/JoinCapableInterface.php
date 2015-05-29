<?php

namespace Spark\Db\Query;

interface JoinCapableInterface
{
    /**
     * Creates and adds a join to the query. 
     *
     * Because a SQL join is equal to a inner join this method acts as an alias to 
     * the {@link JoinCapableInterface::innerJoin($fromAlias, $join, $alias, $condition)} method.
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function join($join, $alias, $condition);

    /**
     * Creates and adds a inner join to the query.
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function innerJoin($join, $alias, $condition);

    /**
     * Creates and adds a left join to the query.
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function leftJoin($join, $alias, $condition);
    
    /**
     * Creates and adds a right join to the query.
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function rightJoin($join, $alias, $condition);    
}
