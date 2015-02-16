<?php

namespace Framework\Common;

/**
 * The CounterInterface is a general interface that can serve multiple purposes.
 * 
 * A class that implements this interface could be used as a countdown event which will trigger
 * an event once the counter has reached zero, or another purpose might be a counter that tracks
 * the number of occurrences of a character, symbol or instances of a particular class.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface CounterInterface
{
    /**
     * Returns the current value of the counter.
     *
     * @return int the current value.
     */
    public function getValue();
    
    /**
     * Change the current value of the counter
     *
     * @param int $offset the amount by which to change the counter's value. Can be negative.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     */
    public function bumpValue($offset);
    
    /**
     * Reset the current value of the counter.
     */
    public function resetValue();
    
    /**
     * Increment the current value by one.
     */
    public function incrementValue();
    
    /**
     * Decrement the current value by one.
     */
    public function decrementValue();
}
