<?php

namespace Spark\Db\Sql;

class Limit
{
    /**
     * The amount of results to retrieve.
     * 
     * @var int|null
     */
    private $limit = null;
    
    /**
     * Create a new limit.
     *
     * @param int $limit the amount of results to retrieve.
     */
    public function __construct($limit)
    {
        $this->setLimit($limit);
    }
    
    /**
     * Set the amount of results to retrieve.
     *
     * @param int|null $limit the amount of result results to retrieve, or null to remove any previously limit.
     * @throws InvalidArgumentException if the given argument is not a number or null literal.
     * @throws LogicException if the given limit is a negative number.
     */
    public function setLimit($limit = null)
    {
        if (!(is_numeric($limit) || $limit === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument or null literal; received "%s"',
                __METHOD__,
                (is_object($limit)) ? get_class($limit) : gettype($limit)
            ));
        } else if (is_numeric($limit) && $limit < 0) {
            throw new \LogicException(sprintf(
                '%s: expects an absolute value; recived "%s"',
                __METHOD__,
                $limit
            ));
        }
        
        $this->limit = (is_numeric($limit)) ? (int) $limit : null;
    }
    
    /**
     * Returns the amount of results to retrieve, or null if no limit is set.
     * 
     * @return int|null the amount of results to retrieve, or null.
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $limit = '';
        if (($value = $this->getLimit()) !== null) {
            $limit = sprintf('LIMIT %d', $value);
        }
        
        return $limit;
    }
}
