<?php

namespace Framework\Sort\Comparator;

/**
 * A comparison function, which allows the ordering on a collection of objects. A comparator is specifically designed to work with 
 * user-defined comparison functions that are built-in to the PHP programming language. So in order to sort a collection of objects 
 * the {@link ComparatorInterface::compare($firstValue, $secondValue} method should be passed to a sort function (such as usort).
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ComparatorInterface
{
    /**
     * Compares both arguments for order. Returns a negative integer, zero or positive integer as the first argument is less than, 
     * equal to, or greater than the second. 
     *
     * @param mixed $firstValue the first value to be compared.
     * @param mixed $secondValue the second value to be compared.
     * @return int a negative, zero or positive integer as the first argument is less than, equal to or greater than the second.         
     */
    public function compare($firstValue, $secondValue);
}
