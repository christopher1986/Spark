<?php

namespace Framework\Collection;

/**
 * This class implements the {@see MapInterface}, and is backed by a native array.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Map implements MapInterface
{
    /**
     * A native array to hold elements.
     *
     * @var array
     */
    private $items = array();

    /**
     * Indicate if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;

    /**
     * {inheritDoc}
     */
    public function put($key, $value)
    {
        if ($key === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: does not allow a key to be NULL; received "%s"',
                __METHOD__,
                (is_object($key) ? get_class($key) : gettype($key))
            ));
        }

        $retval = $this->get($key);
        if ($retval !== $value) {
            $this->items[$key] = $value;
        }
        return $retval;
    }
    
    /**
     * {inheritDoc}
     */
    public function putAll(MapInterface $map)
    {
        if ($key === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a Map as argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        }
        
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }
    }
    
    /**
     * {inheritDoc}
     */
    public function clear()
    {
        $this->items = array();
    }
    
    /**
     * {inheritDoc}
     */
    public function containsKey($key)
    {
        return (!$this->isEmpty() && isset($this->items[$key]));
    }
    
    /**
     * {inheritDoc}
     */
    public function containsValue($value)
    {
        return (!$this->isEmpty() && (false !== array_search($value, $this->items));
    }
    
    /**
     * {inheritDoc}
     */
    public function get($key)
    {
        return ($this->containsKey($key)) ? $this->items[$key] : null;
    }

    /**
     * {inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }
    
    /**
     * {inheritDoc}
     */
    public function remove($key)
    {
        $retval = null;
        if ($this->containsKey($key)) {
            $retval = $this->get($key);
            unset($this->items[$key]);
        }
        
        return $retval;
    }
    
    /**
     * {inheritDoc}
     */
    public function keySet()
    {
        return new Set(array_keys($this->items));
    }
    
    /**
     * {inheritDoc}
     */
    public function values()
    {
        return new ArrayList(array_values($this->items));
    }
    
    /**
     * {inheritDoc}
     */
    public function toArray()
    {
        return $this->items;
    }
    
    /**
     * Returns the number of elements contained by this map.
     *
     * @return int the number of elements contained by this map.
     */
    public function count()
    {
        return (count($this->items));
    }
    
    /**
     * Returns the current element.
     *
     * @return mixed the current element.
     */
    public function current()
    {
        return current($this->items);
    }
    
    /**
     * Returns the key for the current element.
     *
     * @return scalar the key of the current element.
     */
    public function key()
    {
        return key($this->items);
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->items)); 
    }
    
    /**
     * Rewind the iterator to the first element.
     *
     * @return void.
     */
    public function rewind()
    {
        $this->valid = (false !== reset($this->items));
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return bool true if the current position is valid, false otherwise.
     */
    public function valid()
    {
        return $this->valid;
    }
}
