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

namespace Spark\Db\Sql;

/**
 * The From class represents the table to retrieve results from.
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
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
     * The join condition(s).
     *
     * @var CompositeExpression
     */
    private $conditions;
    
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
        $this->conditions = new CompositeExpression();
    
        $this->setTable($table);
        $this->setAlias($alias);
        $this->setCondition($condition);
        $this->setType($type);
    }
    
    /**
     * Add one or more join conditions. Creates a logical 'AND' relation with any previous conditions,
     * and any previously conditions set will be removed.
     *
     * @param string|array $conditions one or more conditions.
     */
    public function on($conditions)
    {
        $this->conditions->clear();
        return $this->andOn(func_get_args());
    }
    
    /**
     * Add one or more join conditions. Creates a logical 'AND' relation with any previous conditions.
     *
     * @param string|array $expressions one or more expressions.
     */
    public function andOn($expressions)
    {
        $expressions = (is_array($expressions)) ? $expressions : func_get_args();        
        $this->createCondition($expressions, CompositeExpression::TYPE_AND);
        
        return $this;
    }
    
    /**
     * Add one or more join conditions. Creates a logical 'OR' relation with any previous conditions.
     *
     * @param string|array $expressions one or more expressions.
     */
    public function orOn($expressions)
    {
        $expressions = (is_array($expressions)) ? $expressions : func_get_args();        
        $this->createCondition($expressions, CompositeExpression::TYPE_OR);
        
        return $this;
    }
    
    /**
     * Set the table join with.
     *
     * @param string $table the table to join with.
     * @throws InvalidArgumentException if the given argument is not a 'string' type.
     */
    private function setTable($table)
    {
	    if (!is_string($table)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($table)) ? get_class($table) : gettype($table)
            ));
	    }
	    
        $this->table = $table;
    }
    
    /**
     * Set alias for table to join with.
     *
     * @param string $alias the table alias to join with.
     * @throws InvalidArgumentException if the given argument is not a 'string' type.
     */
    private function setAlias($alias)
    {
	    if (!is_string($alias)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($alias)) ? get_class($alias) : gettype($alias)
            ));
	    }
	    
        $this->alias = $alias;
    }
    
    /**
     * The condition that applies to the join.
     *
     * @param string|callable $expression a string containing the expression or a callable.
     */
    private function setCondition($expression)
    {        
        if (is_callable($expression)) {
            call_user_func($expression, $this);
        } else {
            $this->on($expression);
        }
    }
    
    /**
     * Set the join type.
     *
     * @param int $type the join type.
     * @throws InvalidArgumentException if the given type is not one of the Join constants.
     */
    private function setType($type)
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
        if (array_key_exists($this->type, $joinTypes)) {
            $joinType = $joinTypes[$this->type];
        }
        
        return $joinType;
    }
    
    /**
     * Creates a composite of join conditions
     *
     * @param array $expressions a collection containing zero or more expressions.
     * @param int $type the relationship between the one or more join conditions.
     */
    private function createCondition(array $expressions, $type = CompositeExpression::TYPE_AND)
    {
        $conditions = $this->conditions;
        if ($conditions->getType() === $type) {
            $conditions->addAll($expressions);
        } else {
            array_unshift($expressions, $conditions);
            $this->conditions = new CompositeExpression($type, $expressions);
        }
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        return sprintf('%1$s JOIN %2$s AS %3$s ON %4$s', $this->getJoinType(), $this->table, $this->alias, $this->conditions);
    }
}
