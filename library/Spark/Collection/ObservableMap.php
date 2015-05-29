<?php

namespace Spark\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

use Spark\EventDispatcher\EventDispatcher;
use Spark\Collection\Event\CollectionChangeEvent;

/**
 * The ObservableMap is similar to an associative array and will map keys to values. A key can only be mapped to a single value, 
 * and for that reason it's impossible to store duplicate keys. An ObservableMap provides notifications to registered listeners
 * informing of changes that have occured to the map, such as elements being added or removed from the map.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class ObservableMap implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * A native array to hold the elements.
     * 
     * @var array
     */
    private $container = array();

    /**
     * A dispatcher to notify any listeners.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    /**
     * Create a new observable map.
     *
     * @param array $elements (optional) the initial elements this map will hold.
     */
    public function __construct(array $elements = array())
    {
        $this->container = $elements;
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Tests whether the given offset exists.
     *
     * @param mixed $offset the offset whose presence will be tested.
     * @return bool true if the offset exists, false otherwise.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     */
    public function offsetExists($offset)
    {
        return (isset($this->container[$offset]));
    }
    
    /**
     * Returns the value associated with the given offset. 
     *
     * @param mixed $offset the offset whose value to retrieve.
     * @return mixed the value for the given offset, or null on failure.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->container[$offset] : null;
    }
    
    /**
     * Assign a value to the specified offset.
     *
     * @param mixed $offset the offset to assign the value.
     * @param mixed $value the value to set.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
        
        $event = new CollectionChangeEvent($this, $offset, CollectionChangeEvent::ADDED);
        $this->dispatcher->dispatch(CollectionChangeEvent::EVENT_CHANGED, $event);
    }
    
    /**
     * Removes the given offset and the value associated with the offset.
     *
     * @param mixed $offset the offset to remove.
     * @return void
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
        
        $event = new CollectionChangeEvent($this, $offset, CollectionChangeEvent::REMOVED);
        $this->dispatcher->dispatch(CollectionChangeEvent::EVENT_CHANGED, $event);
    }
    
    /**
     * Returns an iterator over the elements in this map in proper sequence.
     *
     * @return ArrayIterator an (external) iterator for the elements contained by this map. 
     * @link http://php.net/manual/en/class.iteratoraggregate.php
     */
    public function getIterator()
    {
        return new ArrayIterator($this->container);
    }
    
    /**
     * Returns the number of elements contained by this map.
     *
     * @return int the number of elements contained by this map.
     * @link http://php.net/manual/en/class.countable.php
     */
    public function count()
    {
        return count($this->container);
    }
    
    /**
     * Add a listener to this observable map.
     *
     * @param callable $eventHandler the handler to register.
     * @param int $priority the higher the priority, the earlier the handler will be triggered.
     */
    public function addListener($eventHandler, $priority = 0)
    {
        $this->dispatcher->addEventHandler(CollectionChangeEvent::EVENT_CHANGED, $eventHandler, $priority = 0);
    }
    
    /**
     * Remove a listener from this observable map
     *
     * @param callable $eventHandler the handler to unregister.
     */
    public function removeListener($eventHandler)
    {
        $this->dispatcher->removeEventHandler(CollectionChangeEvent::EVENT_CHANGED, $eventHandler);
    }
}
