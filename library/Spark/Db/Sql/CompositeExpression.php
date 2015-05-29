<?php

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
     * Returns the clause type.
     *
     * @return string the clause type.
     */
    public function getType()
    {
        return $this->type;
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
        return (!empty($expression) || ($expression instanceof self && !$expression->isEmpty()));
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
}
