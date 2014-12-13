<?php

namespace Framework\Sort\Comparator;

/**
 * This class provides a skeleton implementation of the Comparable interface, to minimize the effort required to implement this interface.
 *
 * In order to use this class a developer only needs to implements the abstract methods which define if a comparator can handle both values
 * and if so how both values should be ordered.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class AbstractComparator implements ComparatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare($firstValue, $secondValue)
    {
        $retval = 0;
        if ($this->accepts($firstValue, $secondValue)) {
            $retval = $this->internalCompare($firstValue, $secondValue);
        }
        return $retval;
    }
    
    /**
     * Returns true if this comparator is capable comparing both values, otherwise false should be returned allowing another comparator
     * a change to compare both values.
     * 
     * @param mixed $firstValue the first value to be compared.
     * @param mixed $secondValue the second value to be compared.
     * @return bool true if a comparator is capable of comparing both values, otherwise false.
     */
    public abstract function accepts($firstValue, $secondValue);
    
    /**
     * Compares both arguments for order. Returns a negative integer, zero or positive integer as the first argument is less than, 
     * equal to, or greater than the second. 
     *
     * @param mixed $firstValue the first value to be compared.
     * @param mixed $secondValue the second value to be compared.
     * @return int a negative, zero or positive integer as the first argument is less than, equal to or greater than the second.
     *              
     */
    protected abstract function internalCompare($firstValue, $secondValue);
}
