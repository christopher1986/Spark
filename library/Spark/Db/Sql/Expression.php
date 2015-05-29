<?php

namespace Spark\Db\Sql;

class Expression implements ExpressionInterface
{
    /**
     * The SQL expression.
     *
     * @var string
     */
    private $expression;
    
    /**
     * Create a new expression.
     *
     * @param string $expression (optional) the SQL expression.
     */
    public function __construct($expression = '')
    {
        $this->setExpression($expression);
    }
    
    /**
     * Set a SQL expression.
     *
     * @param string $expression the SQL expression.
     */
    public function setExpression($expression)
    {
        if (!is_string($expression)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($expression)) ? get_class($expression) : gettype($expression)
            )); 
        }
        
        $this->expression = $expression;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getExpression()
    {
        return $this->expression;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        return $this->getExpression();
    }
}
