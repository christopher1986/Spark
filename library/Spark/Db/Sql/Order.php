<?php

namespace Spark\Db\Sql;

class Order
{
    /**
     * Indicates that sorting is ascending.
     *
     * @var string
     */
    const SORT_ASC = 'ASC';
    
    /**
     * Indicates that sorting is descending.
     *
     * @var string
     */
    const SORT_DESC = 'DESC';

    /**
     * The identifier.
     *
     * @var IdentifierInterface
     */
    private $identifier;
    
    /**
     * The sorting either ascending or descending.
     *
     * @var string
     */
    private $sort = '';
    
    /**
     * Create a new order.
     *
     * @param string $identifier the identifier for which this order is created.
     * @param string $sort the sorting either ascending or descending.
     */
    public function __construct(IdentifierInterface $identifier, $sort = self::SORT_ASC)
    {
        $this->setIdentifier($identifier);
        $this->setSort($sort);
    }
    
    /**
     * Set the identifier to which this order applies.
     *
     * @param IdentifierInterface $identifier the identifier this alias represents.
     */
    public function setIdentifier(IdentifierInterface $identifier)
    {    
        $this->identifier = $identifier;
    }
    
    /**
     * Returns the identifier to which this order applies.
     *
     * @var IdentifierInterface the identifier.
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * Determine how the results shoud be sorted.
     *
     * @param string $sort the sorting to apply.
     * @throws InvalidArgumentException if the given sorting is not one of the Order constants.
     */
    public function setSort($sort)
    {
        $sort = strtoupper($sort);
        if (!in_array($sort, array(self::SORT_ASC, self::SORT_DESC))) {
            throw new \InvalidArgumentException(sprintf(
                '%s: unable to determine what sorting should be applied; received "%s"',
                __METHOD__,
                (is_object($sort)) ? get_class($sort) : $sort
            ));
        }
        
        $this->sort = $sort;
    }
    
    /**
     * Returns the sorting.
     *
     * @return string the sorting.
     */
    public function getSort()
    {
        return $this->sort;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $order = (string) $this->getIdentifier();
        if (($sort = $this->getSort()) !== '') {
            $order = sprintf('%s %s', $order, $sort);
        }
        
        return $order;
    }
}
