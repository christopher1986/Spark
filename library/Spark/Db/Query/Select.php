<?php

namespace Spark\Db\Query;

use Spark\Db\Driver\Composer\Visitor\HierarchicalVisitorInterface;
use Spark\Db\Sql\Alias;
use Spark\Db\Sql\CompositeExpression;
use Spark\Db\Sql\Expression;
use Spark\Db\Sql\From;
use Spark\Db\Sql\Identifier;
use Spark\Db\Sql\Join;
use Spark\Db\Sql\Limit;
use Spark\Db\Sql\Offset;
use Spark\Db\Sql\Order;

class Select extends AbstractStatement implements FilterCapableInterface, JoinCapableInterface, OffsetCapableInterface, OrderCapableInterface
{
    /**
     * Indicates that nothing has changed.
     *
     * @var int
     */
    const IS_CLEAN = 0x01;
    
    /**
     * Indicates that the underlying data has changed.
     * 
     * @var int
     */
    const IS_DIRTY = 0x02;

    /**
     * The current state.
     *
     * @var int
     */
    private $state = self::IS_DIRTY;

    /**
     * A (previously) generated query.
     *
     * @var string
     */
    private $query = '';

    /**
     * The parts that form the select statement.
     *
     * @var array
     */
    private $parts = array(
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
     * @param string the table name.
     * @param string $alias (optional) the alias for this table, joins however do require an alias.
     */
    public function from($from, $alias = '')
    {
        $from = new Identifier($from);
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
        
        $this->addQueryPart('select', new Expression($expression));        
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
            $expressions[] = new Identifier($group);
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
        $this->addQueryPart('orderBy', new Order(new Identifier($order), $sort));        
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
        
        $query = sprintf('SELECT %s FROM %s ', $this->listColumns($this->parts['select']), $this->parts['from']);
        if (!empty($this->parts['join'])) {
            $query .= sprintf('%s ', implode(' ', $this->parts['join']));
        }
        if (($this->parts['where'] instanceof CompositeExpression) && !$this->parts['where']->isEmpty()) {
            $query .= sprintf('WHERE %s ', $this->parts['where']);
        }
        if (!empty($this->parts['groupBy'])) {
            $query .= sprintf('GROUP BY %s ', $this->listColumns($this->parts['groupBy']));
        }
        if (($this->parts['having'] instanceof CompositeExpression) && !$this->parts['having']->isEmpty()) {
            $query .= sprintf('HAVING %s ', $this->parts['having']);
        }
        if (!empty($this->parts['orderBy'])) {
            $query .= sprintf('ORDER BY %s ', $this->listColumns($this->parts['orderBy']));
        }
        if (!empty($this->parts['limit']) || !empty($this->parts['offset'])) {
            $limit = ($this->parts['limit'] instanceof Limit) ? $this->parts['limit']->getLimit() : null;
            $offset = ($this->parts['offset'] instanceof Offset) ? $this->parts['offset']->getOffset() : null;            
            $query .= $this->limitResults($limit, $offset);
        }
        
        // update state of object.
        $this->setState(self::IS_CLEAN);
        // store generated SQL statement.
        $this->query = $query;

        return $this->query;
    }
    
    /**
     * Tests whether this object is dirty.
     *
     * @return bool true if this object is dirty, false otherwise.
     */
    public function isDirty()
    {
        return ($this->state === self::IS_DIRTY);    
    }
    
    /**
     * Tests whether this object is clean.
     *
     * @return bool true if this object is clean, false otherwise.
     */
    public function isClean()
    {
        return ($this->state === self::IS_CLEAN);
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
        
        $identifier = new Identifier($parts[0]);
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
     * @param array $where a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more where clauses.
     */
    private function createWhere(array $wheres, $type = CompositeExpression::TYPE_AND)
    {        
        $expressions = array();
        foreach ($wheres as $where) {
            $expressions[] = new Expression($where);
        }

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
     * @param array $havings a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more where clauses.
     */
    private function createHaving(array $havings, $type = CompositeExpression::TYPE_AND)
    {        
        $expressions = array();
        foreach ($havings as $having) {
            $expressions[] = new Expression($having);
        }

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
    
    /**
     * Add a new query part with the given name.
     *
     * @param string $name the name of the query part.
     * @param mixed $part the part to add.
     * @param bool $append (optional) if true will append the part, otherwise all existing parts are first removed.
     */
    private function addQueryPart($name, $part, $append = true)
    {
        if (!$append) {
            $this->clearQueryPart($name);
        }

        if (isset($this->parts[$name])) {
            $queryParts = $this->parts[$name];
            if (is_array($queryParts)) {
                if (!is_array($part)) {
                    $queryParts[] = $part;
                } else {   
                    $queryParts = array_merge($queryParts, $part);
                }
            } else {
                $queryParts = $part;
            }
            
            $this->parts[$name] = $queryParts;
        } else {
            $this->parts[$name] = $part;
        }
        
        $this->setState(self::IS_DIRTY);
    }
    
    /**
     * Returns if present the query part with the give name, otherwise the default value is returned.
     *
     * @param string $name the name of the query part to return.
     * @param mixed $default the value to return if no part exists for the given name.
     * @return mixed the part associated with the given name, or the default value.
     */
    public function getQueryPart($name, $default = array())
    {
        return (array_key_exists($name, $this->parts)) ? $this->parts[$name] : $default;
    }
    
    /**
     * Replaces all parts for the query part with the given name with the initial value.
     *
     * @param string $name the name of the query part to remove.
     * @param mixed $initial the value to reset the empty query part to.
     */
    private function clearQueryPart($name, $initial = null)
    {
        $this->parts[$name] = $initial;
        $this->setState(self::IS_DIRTY);
    }
    
    /**
     * Set the state of this object.
     *
     * @param int $state the state.
     */
    private function setState($state)
    {
        $this->state = $state;
    }
    
    /**
     * Returns a string representation of this select statement.
     * 
     * @return string a string representation of this select statement.
     */
    public function __toString()
    {
        return $this->getSqlString();
    }
}
