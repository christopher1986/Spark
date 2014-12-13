<?php

namespace Framework\EventDispatcher;

use Framework\Sort\Comparable;

/**
 * The EventItem is reponsible for storing event listeners. The priority of an EventItem is used to determine it's position 
 * within a collection that is sorted.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class EventItem implements Comparable
{    
    /**
     * A listener to be triggered for specific event(s).
     *
     * @var callable
     */
    private $listener;

    /**
     * The priority of the listener.
     *
     * @var int
     */
    private $priority;
    
    /**
     * Construct a new EventItem.
     *
     * @param callable $listener the listener to store.
     * @param int $priority the priority of the listener.
     */
    public function __construct($listener, $priority = 0)
    {
        $this->setListener($listener);
        $this->setPriority($priority);
    }
    
    /**
     * Set the listener.
     *
     * @param callable $listener a listener that is triggered for specific event(s).
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
    }
    
    /**
     * Returns the listener.
     *
     * @return callable a listener that is triggered for specific event(s).
     */
    public function getListener()
    {
        return $this->listener;
    }
    
    /**
     * Set the priority of the listener.
     *
     * @param int $priority the priority of the listener, defaults to 0.
     * @throws InvalidArgumentException if the given argument is not numeric.
     */
    public function setPriority($priority = 0) 
    {
        if (!is_numeric($priority)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($priority) ? get_class($priority) : gettype($priority))
            ));
        }
        
        $this->priority = (int) $priority;
    }
    
    /**
     * Returns the priority of the listener.
     *
     * @return int the priority of the listener.
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * {@inheritDoc}
     */
    public function compareTo($obj)
    {
        if ($obj instanceof EventItem) {
            if ($obj->getPriority() == $this->getPriority()) {
                return 0;
            }
            return ($this->getPriority() > $obj->getPriority()) ? 1 : -1;
        }
        return 0;
    }
}
