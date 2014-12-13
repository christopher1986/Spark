<?php

namespace Framework\EventDispatcher;

/**
 * The Event class contains event data that a dispatcher can send to any listener. More specific events can subclass this 
 * base class to add specific information to event being dispatched.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Event
{
    /**
     * a flag that determines if this event is consumed.
     *
     * @var bool.
     */
    protected $consumed = false;
    
    /**
     * the dispatcher from which this event was dispatched.
     * 
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    
    /**
     * the name of this event.
     * 
     * @var string
     */
    protected $name;

    /**
     * Construct a new event that can be dispatched to listeners.
     *
     * @param string $name the name of this event.
     * @param EventDispatcherInterface $dispatcher the dispatcher from which this event was dispatched.
     */
    public function __construct($name = null, EventDispatcherInterface $dispatcher = null)
    {
        if ($name !== null) {
         $this->setName($name);
        }
        
        if ($dispatcher !== null) {
            $this->setDispatcher($dispatcher);
        }
    }
    
    /**
     * Returns true if this event has been consumed and it should not propagate any further.
     *
     * @return bool true if the event has been consumed, false otherwise.
     */
    public function isConsumed()
    {
        return $this->consumed;
    }
    
    /**
     * Consume the event and stop it from propagating any further.
     */
    public function consume()
    {
        $this->consumed = true;
    }
    
    /**
     * Set the dispatcher from which this event was dispatched.
     *
     * @param EventDispatcherInterface $dispatcher the dispatcher from which this event was dispatched.
     */ 
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Get the dispatcher from which this event was dispatched.
     *
     * @return EventDispatcherInterface returns the dispatcher from which this event was dispatched.
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
    
    /**
     * Set the name for this event.
     *
     * @param string $name the name of this event.
     */
    public function setName($name)
    {
        if (!is_null($name) && !is_string($name)) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s must be of the type string, %s given', __METHOD__, gettype($name)));
        }
        
        $this->name = $name;
    }
    
    /**
     * Returns the name for this event.
     *
     * @return string the name of this event.
     */
    public function getName()
    {
        return $this->name;
    }
}
