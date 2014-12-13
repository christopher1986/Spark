<?php

namespace Framework\EventDispatcher;

use Framework\Collection\PriorityList;

/**
 * The EventDispatcher class is the base class for all classes that dispatch events. Use this class to register listeners for specific
 * events and notify listeners by dispatching events.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * A collection of event filters.
     *
     * @var array
     */
    private $filters = array();
    
    /**
     * A collection of event handler.
     *
     * @var array
     */
    private $handlers = array();
     
    /**
     * {inheritDoc}
     */
    public function addEventFilter($eventName, $eventFilter, $priority = 0)
    {
        // create a priority list for non existing events.
        if (!$this->hasEventFilters($eventName)) {
            $this->filters[$eventName] = new PriorityList();
        }
        
        $eventItems = $this->filters[$eventName];
        $eventItems->add(new EventItem($eventFilter, $priority));
    }
    
    /**
     * {inheritDoc}
     */
    public function removeEventFilter($eventName, $eventFilter)
    {
        if ($this->hasEventFilters($eventName)) {
            $eventItems = $this->filters[$eventName];
            // iterate over a (copy) array to prevent non-deterministic behavior.
            $tmp = $eventItems->toArray();
            foreach ($tmp as $eventItem) {
                if ($eventItem->getListener() === $eventFilter) {
                    $eventItems->remove($eventItem);
                }
            }
        }
    }
    
    /**
     * {inheritDoc}
     */
    public function getEventFilters($eventName = null)
    {
        $eventFilters = array();
        if ($this->hasEventFilters($eventName)) {
            $eventItems = $this->filters[$eventName];
            foreach ($eventItems as $eventItem) {
                $eventFilters[] = $eventItem->getListener();
            }
        }
        return $eventFilters;
    }
    
    /**
     * {inheritDoc}
     */
    public function hasEventFilters($eventName)
    {
        $retval = false;
        if (isset($this->filters[$eventName])) {
            $retval = (bool) count($this->filters[$eventName]);
        }
        return $retval;
    }
     
    /**
     * {inheritDoc}
     */
    public function addEventHandler($eventName, $eventHandler, $priority = 0)
    {
        // create a priority list for non existing events.
        if (!$this->hasEventHandlers($eventName)) {
            $this->handlers[$eventName] = new PriorityList();
        }
        
        $eventItems = $this->handlers[$eventName];
        $eventItems->add(new EventItem($eventHandler, $priority));
    }
    
    /**
     * {inheritDoc}
     */
    public function removeEventHandler($eventName, $eventHandler)
    {
        if ($this->hasEventHandlers($eventName)) {
            $eventItems = $this->handlers[$eventName];
            // use a (copy) array to prevent non-deterministic behavior.
            $tmp = $eventItems->toArray();
            foreach ($tmp as $eventItem) {
                if ($eventItem->getListener() === $eventHandler) {
                    $eventItems->remove($eventItem);
                }
            }
        }
    }
    
    /**
     * {inheritDoc}
     */
    public function getEventHandlers($eventName = null)
    {
        $eventHandlers = array();
        if ($this->hasEventHandlers($eventName)) {
            $eventItems = $this->handlers[$eventName];
            foreach ($eventItems as $eventItem) {
                $eventHandlers[] = $eventItem->getListener();
            }
        }
        return $eventHandlers;
    }
    
    /**
     * {inheritDoc}
     */
    public function hasEventHandlers($eventName)
    {
        $retval = false;
        if (isset($this->handlers[$eventName])) {
            $retval = (bool) count($this->handlers[$eventName]);
        }
        return $retval;
    }
    
    /**
     * {inheritDoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        $event = ($event !== null) ? $event : new Event();
        $event->setDispatcher($this);
        $event->setName($eventName);
        
        // dispatch event capturing phase.
        $event = $this->dispatchCapturingEvent($eventName, $event);
        if ($event->isConsumed()) {
            return null;
        }
        
        // dispatch event bubbling phase.
        $event = $this->dispatchBubblingEvent($eventName, $event);
        if ($event->isConsumed()) {
            return null;
        }
        
        return $event;
    }
    
    /**
     * Dispatch event to all event filters, this is also known as the event capturing phase.
     * 
     * An event filter can give addtional information to the event or consume the event which
     * will prevent additional filters or event handlers from receiving the event.
     *
     * @param string $eventName the name of the event to dispatch.
     * @param Event|null the event to dispatch.
     * @return Event the event that was dispatched.
     */
    private function dispatchCapturingEvent($eventName, Event $event)
    {
        $filters = $this->getEventFilters($eventName);
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                call_user_func($filter, $event);
                if ($event->isConsumed()) {
                    break;
                }     
            }
        }
        return $event;
    }
    
    /**
     * Dispatch event to all event handlers, this is also known as the event bubbling phase.
     * 
     * An event handler can consume the event at any point which will prevent additional handlers
     * from receiving the event.
     *
     * @param string $eventName the name of the event to dispatch.
     * @param Event|null the event to dispatch.
     * @return Event the event that was dispatched.
     */
    private function dispatchBubblingEvent($eventName, Event $event)
    {
        $handlers = $this->getEventHandlers($eventName);
        if (!empty($handlers)) {
            foreach ($handlers as $handler) {
                call_user_func($handler, $event); 
                if ($event->isConsumed()) {
                    break;
                }  
            }
        }
        return $event;
    }
}
