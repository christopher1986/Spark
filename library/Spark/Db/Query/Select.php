<?php
/**
 * Copyright (c) 2015, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2015 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Db\Query;

use Spark\Db\Sql\Alias;
use Spark\Db\Sql\CompositeExpression;
use Spark\Db\Sql\From;
use Spark\Db\Sql\Join;
use Spark\Db\Sql\Limit;
use Spark\Db\Sql\Offset;
use Spark\Db\Sql\Order;

class Select extends AbstractSql implements FilterCapableInterface, JoinCapableInterface, OffsetCapableInterface, LimitCapableInterface, OrderCapableInterface
{
    /**
     * The parts that form the select statement.
     *
     * @var array
     */
    protected $parts = array(
        'select'  => array(),
        'from'    => null,
        'join'    => array(),
        'where'   => null,
        'having'  => null,
        'groupBy' => array(),
        'orderBy' => array(),
        'offset'  => null,
        'limit'   => null,
    );
    
    /**
     * Specify from which table to retrieve rows.
     *
     * @param string $from the table name.
     * @param string $alias (optional) the alias for this table, joins however do require an alias.
     * @throws InvalidArgumentException if the first argument is not a 'string' type.
     */
    public function from($from, $alias = '')
    {
        if (!is_string($from)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($from)) ? get_class($from) : gettype($from)
            ));
        }
    
        if (is_string($alias) && $alias !== '') {
            $from = new Alias($from, $alias);
        }
        
        $this->addQueryPart('from', $from, false);
        return $this;
    }

    /**
     * Specify which columns to retrieve. All previously set select statement(s) will be removed, 
     * use the {@link Select::addSelect($select)} method to add additional statements instead.
     *
     * @param string|array|Traversable $select either a string for a single column or a collection for multiple columns.
     */
    public function select($select)
    {
        $selects = (is_array($select)) ? $select : func_get_args();  

        $this->clearQueryPart('select', array());   
        $this->addSelect($selects);
        
        return $this; 
    }
    
    /**
     * Specify which columns to retrieve.
     *
     * @param string|array|Traversable $select either a string for a single column or a collection for multiple columns.
     */
    public function addSelect($select)
    {           
        $selects = (is_array($select)) ? $select : func_get_args();
        foreach ($selects as $select) {
            $this->createSelect($select);
        }
        
        return $this; 
    }
    
    /**
     * Specify which column to retrieve using a (raw) vendor-specific expression. All previously 
     * set select statement(s) will be removed, use the {@link Select::addRawSelect($select)} method 
     * to add additional vendor-specific statements instead.
     *
     * Unlike the {@link Select::select($select)} method which allows you to pass a collection of
     * columns this method only supports a single expression, you can however call this method numerous
     * times until all expressions have been added.
     *
     * @param string $expression a raw expression.
     * @throws InvalidArgumentException if the given argument is not a 'string'type.
     */
    public function rawSelect($expression)
    {
        $this->clearQueryPart('select', array());
        $this->addRawSelect($expression);
        
        return $this; 
    }
    
    /**
     * Specify which column to retrieve using a (raw) vendor-specific expression.
     *
     * Unlike the {@link Select::addSelect($select)} method which allows you to pass a collection of
     * columns this method only supports a single expression, you can however call this method numerous
     * times until all vendor-specific expressions have been added.
     *
     * @param string $expression a raw expression.
     * @throws InvalidArgumentException if the given argument is not a 'string'type.
     */
    public function addRawSelect($expression)
    {
        if (!is_string($expression)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expected a string containing a vendor-specific expression; received "%s"',
                __METHOD__,
                (is_object($expression)) ? get_class($expression) : gettype($expression)
            ));
        }
        
        $this->addQueryPart('select', $expression);        
        return $this; 
    }
    
    /**
     * {@inheritdoc}
     */
    public function join($join, $alias, $condition)
    {
        $this->createJoin($join, $alias, $condition, Join::TYPE_INNER_JOIN);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function innerJoin($join, $alias, $condition)
    {
        $this->createJoin($join, $alias, $condition, Join::TYPE_INNER_JOIN);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function leftJoin($join, $alias, $condition)
    {
        $this->createJoin($join, $alias, $condition, Join::TYPE_LEFT_JOIN);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rightJoin($join, $alias, $condition)
    {
        $this->createJoin($join, $alias, $condition, Join::TYPE_RIGHT_JOIN);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where($where)
    {        
        $this->clearQueryPart('where');        
        return $this->andWhere(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function andWhere($where)
    {
        $clauses = (is_array($where)) ? $where : func_get_args();        
        $this->createWhere($clauses, CompositeExpression::TYPE_AND);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function orWhere($where)
    {
        $clauses = (is_array($where)) ? $where : func_get_args();
        $this->createWhere($clauses, CompositeExpression::TYPE_OR);
        
        return $this;
    }

    /**
     * Specifies a grouping over the results of the query.
     *
     * @param string|array $groups one or more columns to group by.
     */
    public function groupBy($groups)
    {
        $groups = (is_array($groups)) ? $groups : func_get_args();
    
        $this->clearQueryPart('groupBy');
        $this->addGroupBy($groups);
        
        return $this;
    }
    
    /**
     * Add additional grouping over the results of the query.
     *
     * @param string|array $groups one or more columns to group by.
     */
    public function addGroupBy($groups)
    {
        $groups = (is_array($groups)) ? $groups : func_get_args();
    
        $expressions = array();
        foreach ($groups as $group) {
            $expressions[] = (string) $group;
        }

        $this->addQueryPart('groupBy', $expressions);
               
        return $this; 
    }
    
    /**
     * Add one or more restrictions over the grouping of the returned results, and creates a 
     * logical 'AND' relation with any previous restrictions. Replace any previously restrictions
     * that were set.
     *
     * @param string|array $having one or more restrictions.
     */
    public function having($having)
    {        
        $this->clearQueryPart('having');        
        return $this->andHaving(func_get_args());
    }

    /**
     * Add one or more restrictions over the grouping of the returned results, and creates a 
     * logical 'AND' relation with any previous restrictions.
     *
     * @param string|array $having one or more restrictions.
     */
    public function andHaving($having)
    {
        $clauses = (is_array($having)) ? $having : func_get_args();        
        $this->createHaving($clauses, CompositeExpression::TYPE_AND);
        
        return $this;
    }
    
    /**
     * Add one or more restrictions over the grouping of the returned results, and creates a 
     * logical 'OR' relation with any previous restrictions.
     *
     * @param string|array $having one or more restrictions.
     */
    public function orHaving($having)
    {
        $clauses = (is_array($having)) ? $having : func_get_args();
        $this->createHaving($clauses, CompositeExpression::TYPE_OR);
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function orderBy($order, $sort = Order::SORT_ASC)
    {
        $this->clearQueryPart('orderBy', array());
        $this->addOrderBy($order, $sort);
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addOrderBy($order, $sort = Order::SORT_ASC)
    {
        $this->addQueryPart('orderBy', new Order($order, $sort));        
        return $this; 
    }

    /**
     * {@inheritDoc}
     */
    public function limit($limit = null)
    {
        $this->clearQueryPart('limit');
        if ($limit !== null) {
            $this->addQueryPart('limit', new Limit($limit), false);
        }
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function offset($offset = null)
    {
        $this->clearQueryPart('offset');
        if ($offset !== null) {
            $this->addQueryPart('offset', new Offset($offset), false);
        } 
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSqlString()
    {
        if ($this->isClean()) {
            return $this->query;
        }
        
        $query = sprintf('SELECT %s FROM %s ', implode(', ', $this->parts['select']), $this->parts['from']);
        if (!empty($this->parts['join'])) {
            $query .= sprintf('%s ', implode(' ', $this->parts['join']));
        }
        if (($this->parts['where'] instanceof CompositeExpression) && !$this->parts['where']->isEmpty()) {
            $query .= sprintf('WHERE %s ', $this->parts['where']);
        }
        if (!empty($this->parts['groupBy'])) {
            $query .= sprintf('GROUP BY %s ', implode(', ', $this->parts['groupBy']));
        }
        if (($this->parts['having'] instanceof CompositeExpression) && !$this->parts['having']->isEmpty()) {
            $query .= sprintf('HAVING %s ', $this->parts['having']);
        }
        if (!empty($this->parts['orderBy'])) {
            $query .= sprintf('ORDER BY %s ', implode(', ', $this->parts['orderBy']));
        }
        if (!empty($this->parts['limit']) || !empty($this->parts['offset'])) {           
            $query .= $this->limitResults($this->parts['limit'], $this->parts['offset']);
        }
        
        // update state of object.
        $this->setState(self::IS_CLEAN);
        // store generated SQL statement.
        $this->query = rtrim($query);

        return $this->query;
    }
    
    /**
     * Create an identifier for the given column. The $column may also include an alias 
     * for the column through the use of the 'AS' syntax.
     *
     * @param string $column a single column.
     */
    private function createSelect($column)
    {        
        $parts = array_pad(array_map('trim', preg_split('/(AS|as)/', $column, 2)), 2, '');
        
        $identifier = (string) $parts[0];
        if (is_string($parts[1]) && $parts[1] !== '') {
            $identifier = new Alias($identifier, $parts[1]);
        }
        
        $this->addQueryPart('select', $identifier);
    }
    
    /**
     * Create a join expression for the given arguments.
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     * @param string $type the type of join.
     */
    private function createJoin($join, $alias, $condition, $type = Join::TYPE_INNER_JOIN)
    {
        $this->addQueryPart('join', new Join($join, $alias, $condition, $type));
    }
     
    /**
     * Creates a composite of expressions
     *
     * @param array $expressions a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more where clauses.
     */
    private function createWhere(array $expressions, $type = CompositeExpression::TYPE_AND)
    {        
        $where = $this->getQueryPart('where');
        if ($where instanceof CompositeExpression) {
            if ($where->getType() === $type) {
                $where->addAll($expressions);
            } else {
                array_unshift($expressions, $where);
                $this->addQueryPart('where', new CompositeExpression($type, $expressions), false);
            }
        } else {
            $this->addQueryPart('where', new CompositeExpression($type, $expressions), false);
        }
    }
    
     
    /**
     * Creates a composite of expressions
     *
     * @param array $expressions a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more where clauses.
     */
    private function createHaving(array $expressions, $type = CompositeExpression::TYPE_AND)
    {
        $having = $this->getQueryPart('having');
        if ($having instanceof CompositeExpression) {
            if ($having->getType() === $type) {
                $having->addAll($expressions);
            } else {
                array_unshift($expressions, $having);
                $this->addQueryPart('having', new CompositeExpression($type, $expressions), false);
            }
        } else {
            $this->addQueryPart('having', new CompositeExpression($type, $expressions), false);
        }
    }
}
