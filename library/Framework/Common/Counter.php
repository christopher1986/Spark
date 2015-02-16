<?php

namespace Framework\Common;

/**
 * A basic implementation of the CounterInterface.
 *
 * This class performs no additional checking whatsoever on the current value. If the current value
 * of the counter is not permitted to go below a certain threshold additional logic outside this 
 * Counter object will be required.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Counter implements CounterInterface
{
    /**
     * The initial value.
     *
     * @var int
     */
    private $initial = 0;
    
    /**
     * The current value.
     *
     * @var int
     */
    private $value = 0;
    
    /**
     * Construct a new Counter.
     *
     * @param $initial the initial value.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     */
    public function __construct($initial = 0)
    {
        $this->bumpValue($initial);
        $this->initial = (int) $initial;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function bumpValue($offset)
    {
        if (!is_numeric($offset)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric value; received "%s"',
                __METHOD__,
                $offset
            ));
        }
        
        $this->value += (int) $offset;
    }
    
    /**
     * {@inheritDoc}
     */
    public function resetValue()
    {
        $this->value = $this->initial;
    }
    
    /**
     * {@inheritDoc}
     */
    public function incrementValue()
    {
        $this->value++;
    }
    
    /**
     * {@inheritDoc}
     */
    public function decrementValue()
    {
        $this->value--;
    }
}
