<?php

namespace Framework\Sort;

/**
 * The Comparable interface allows for natural ordering on the objects that implement it.
 *
 * Since this interface is unknown to the PHP programming language it's not possible to use functions that are built-in to the 
 * language like sort, arsort or asort. Instead you should instantiate a Comparator object that is specifically designed to handle
 * Comparable objects and pass it's {@link ComparatorInterface::compare($firstValue, $secondValue)} method to a built-in function 
 * like usort which accepts a user-defined comparison function.
 * 
 * @author Chris Harris
 * @version 1.0.0
 * @see http://php.net/manual/en/function.usort.php
 */
interface Comparable
{
    /**
     * Compares this object with the another object for order. Returns a negative integer, zero or positive integer as the first argument is less than, 
     * equal to, or greater than the second. 
     *
     * @param mixed $obj the object to compare with.
     * @return int a negative, zero or positive integer as the first argument is less than, equal to or greater than the second.
     */
    public function compareTo($obj);
}
