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

use ArrayIterator;

class CompositeExpression implements CompositeInterface
{   
    /**
     * Indicates an AND expression.
     *
     * @var string
     */
    const TYPE_AND = 'AND';
 
    /**
     * Indicates an OR expression.
     *
     * @var string
     */
    const TYPE_OR = 'OR';
    /**
     * A collection of expressions.
     *
     * @var array
     */
    protected $expressions = array();
    
    /**
     * The clause type.
     *
     * @var int
     */
    private $type = self::TYPE_AND;

    /**
     * Create a new composite.
     *
     * @param int $type the clause type.
     * @param array|Traversable $expressions (optional) a collection of expressions.
     */
    public function __construct($type = self::TYPE_AND, $expressions = array())
    {        
        $this->setType($type);
        $this->addAll($expressions);
    }
    
    /**
     * Set the clause type.
     *
     * @param string $type the clause type.
     * @throws InvalidArgumentException if the given type is not one of the Clause constants.
     */
    public function setType($type)
    {
        $allowed = array(
            self::TYPE_AND,
            self::TYPE_OR,
        );
        
        if (!in_array($type, $allowed)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: unable to determine what clause type should be applied; received "%s"',
                __METHOD__,
                (is_object($type)) ? get_class($type) : gettype($type)
            ));
        }
        
        $this->type = $type;
    }
    
    /**
     * Returns the composite type.
     *
     * @return string the composite type.
     */
    public function getType()
    {
        return $this->type;
    }
 
    /**
     * Add a new expressions to this composite.
     *
     * @param string|CompositeExpression $expression a string or composite expression.
     * @throws \InvalidArgumentException if the given argument is not a string or CompositeExpression.
     */
    public function add($expression)
    {
        if (!$this->isAllowed($expression)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a non-empty string or CompositeExpression object as argument; received "%s"',
                __METHOD__,
                (is_object($expression) ? get_class($expression) : gettype($expression))
            ));
        }
        
        $this->expressions[] = $expression;
    }
 
    /**
     * Add a collection of expressions.
     *
     * @param array|Traversable $expressions a collection of expressions.
     * @throws \InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function addAll($expressions)
    {
        if (!is_array($expressions) && !($expressions instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable object as argument; received "%s"',
                __METHOD__,
                (is_object($expressions) ? get_class($expressions) : gettype($expressions))
            ));
        }
        
        if ($expressions instanceof \Traversable) {
            $expressions = iterator_to_array($expressions);
        }

        $this->expressions = array_merge($this->expressions, array_filter($expressions, array($this, 'isAllowed')));
    }
    
    /**
     * Removes all expressions from this composite. The composite will be empty after this call returns.
     *
     * @return void.
     */
    public function clear()
    {
        $this->expressions = array();
    }
    
    /**
     * Returns the number of expressions contained by this composite.
     *
     * @return int the number of expressions.
     */
    public function count()
    {
        return count($this->expressions);
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() === 0);
    }
    
    /**
     * Returns an iterator to traverse all the expressions in this composite in proper sequence.
     *
     * @return Iterator an iterator to traverse all the expressions in this composite.
     * @link http://php.net/manual/en/class.iteratoraggregate.php the IteratorAggregate interface
     */
    public function getIterator()
    {
        return new ArrayIterator($this->expressions);
    }
    
    /**
     * Determines whether the given expression is allowed by this composite.
     *
     * @param mixed $expression the expression to be tested.
     * @return bool true if the expression is allowed, false otherwise.
     */
    protected function isAllowed($expression)
    {
        return ((is_string($expression) && strlen($expression) > 0) || ($expression instanceof self && !$expression->isEmpty()));
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        if (count($this) === 1) {
            return (string) reset($this->expressions);
        }
    
        // will be either 'AND' or 'OR'.
        $glue = sprintf(') %s (', $this->getType());
        return sprintf('(%s)', implode($glue, $this->expressions));
    }
    
    /**
     * Create a copy of this expression in it's current state.
     *
     * When cloning an object it's pointers will be copied. This means that any changes made to a cloned object will
     * still be reflected on the original object. So by cloning all objects we ensure that a deep copy is performed.
     *
     * @link http://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        foreach ($this->expressions as $index => $expression) {
            if (is_object($expression)) {
                $this->expressions[$index] = clone $expression;
            }
        }
    }
}
