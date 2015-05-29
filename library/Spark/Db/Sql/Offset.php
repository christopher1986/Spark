<?php

namespace Spark\Db\Sql;

class Offset
{
    /**
     * The offset at which to start retrieving results.
     * 
     * @var int|null
     */
    private $offset;
    
    /**
     * Create a new offset.
     *
     * @param int $offset the offset which to start retrieving results.
     */
    public function __construct($offset)
    {
        $this->setOffset($offset);
    }
    
    /**
     * Set the offset at which to start retrieving results.
     *
     * @param int $offset the offset which to start retrieving results.
     * @throws InvalidArgumentException if the given argument is not a number.
     * @throws LogicException if the given offset is a negative number.
     */
    public function setOffset($offset)
    {
        if (!(is_numeric($offset) || $offset === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument or null literal; received "%s"',
                __METHOD__,
                (is_object($offset)) ? get_class($offset) : gettype($offset)
            ));
        } else if (is_numeric($offset) && $offset < 0) {
            throw new \LogicException(sprintf(
                '%s: expects an absolute value; recived "%s"',
                __METHOD__,
                $offset
            ));
        }
        
        $this->offset = (int) $offset;
    }
    
    /**
     * Returns the offset at which to start retrieving results.
     * 
     * @return int the offset at which to start retrieving results.
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $offset = '';
        if (($value = $this->getOffset()) !== null) {
            $offset = sprintf('OFFSET %d', $value);
        }
        
        return $offset;
    }
}
