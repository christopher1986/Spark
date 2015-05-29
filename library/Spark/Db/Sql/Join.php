<?php

namespace Spark\Db\Sql;

class Join
{
    /**
     * Indicates a INNER JOIN expression.
     *
     * @var int
     */
    const TYPE_INNER_JOIN = 0x01;

    /**
     * Indicates a LEFT JOIN expression.
     *
     * @var int
     */
    const TYPE_LEFT_JOIN = 0x02;
    
    /**
     * Indicates a LEFT JOIN expression.
     *
     * @var int
     */
    const TYPE_RIGHT_JOIN = 0x04;
        
    /**
     * The table to join with.
     *
     * @var string
     */
    private $table = '';
    
    /**
     * The alias of the table to join with.
     *
     * @var string
     */
    private $alias = '';
    
    /**
     * The join condition.
     *
     * @var string
     */
    private $condition = '';
    
    /**
     * The join type.
     *
     * @var int
     */
    private $type = self::TYPE_INNER_JOIN;
    
    /**
     * Create a new join.
     *
     * @param string $table the table to join with.
     * @param string $alias the table alias.
     * @param string $condition the join condition.
     * @param int $type (optional) the join type.
     */
    public function __construct($table, $alias, $condition, $type = self::TYPE_INNER_JOIN)
    {
        $this->setTable($table);
        $this->setAlias($alias);
        $this->setCondition($condition);
        $this->setType($type);
    }
    
    /**
     * Set the table join with.
     *
     * @param string $table the table to join with.
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
    
    /**
     * Returns the table to join with.
     *
     * @return string the table to join with.
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Set alias for table to join with.
     *
     * @param string $alias the table alias to join with.
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
    /**
     * Returns the alias of a table to join with.
     *
     * @return string the alias of a table to join with.
     */
    public function getAlias()
    {
        return $this->alias;
    }
    
    /**
     * The condition applied to the join.
     *
     * @param string $condition the join condition.
     */
    public function setCondition($condition)
    {
        if (!is_string($condition)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($condition)) ? get_class($condition) : gettype($condition)
            ));
        }
        
        $this->condition = $condition;
    }
    
    /**
     * Returns the condition that applied to the join.
     *
     * @return string the join condition.
     */
    public function getCondition()
    {
        return $this->condition;
    }
    
    /**
     * Set the join type.
     *
     * @param int $type the join type.
     * @throws InvalidArgumentException if the given type is not one of the Join constants.
     */
    public function setType($type)
    {
        $allowed = array(
            self::TYPE_INNER_JOIN,
            self::TYPE_LEFT_JOIN,
            self::TYPE_RIGHT_JOIN
        );
        
        if (!in_array($type, $allowed)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: unable to determine what join type should be applied; received "%s"',
                __METHOD__,
                (is_object($type)) ? get_class($type) : gettype($type)
            ));
        }
        
        $this->type = $type;
    }
    
    /**
     * Returns the join type.
     *
     * @return int the join type.
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        return sprintf('%1$s JOIN %2$s AS %3$s ON %4$s', $this->getJoinType(), $this->getTable(), $this->getAlias(), $this->getCondition());
    }
    
    /**
     * Returns a join type for the given Join expression.
     *
     * @param Join $join the join expression.
     * @return string a join type for the given expression.
     */
    private function getJoinType()
    {
        $joinTypes = array(
            Join::TYPE_INNER_JOIN => 'INNER',
            Join::TYPE_LEFT_JOIN  => 'LEFT',
            Join::TYPE_RIGHT_JOIN => 'RIGHT',
        );
    
        $joinType = reset($joinTypes);
        if (array_key_exists($this->getType(), $joinTypes)) {
            $joinType = $joinTypes[$this->getType()];
        }
        
        return $joinType;
    }
}
