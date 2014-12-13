<?php

namespace Framework\Collection;

/**
 * This class implements the {@see SetInterface}, and is backed by a native array.
 * the order at which elements are added remains unchanged during the lifetime of 
 * this set.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Set extends AbstractSet
{
    /**
     * A native array to hold elements.
     *
     * @var array
     */
    private $data = array();

    /**
     * Indicate if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;
    
    /**
     * Construct a new Set.
     *
     * @param mixed $data one or more elements to add to the set, or null.
     */
    public function __construct($data = null) 
    {
        if (null !== $data) {
            $elements = (!is_array($data) && !($data instanceof \Traversable)) ? array($data) : $data;
            $this->addAll($elements);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($element)
    {
        $modified = false;
        if (!$this->contains($element)) {
            $this->data[] = $element;
            $modified = true;
        }
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return (in_array($element, $this->data));
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        $modified = false;
        if (false !== ($index = array_search($element, $this->data))) {
            unset($this->data[$index]);
            $modified = true;
        }
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->data = array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->data;
    }
    
    /**
     * Returns the number of elements contained by this set.
     *
     * @return int the number of elements contained by this set.
     */
    public function count()
    {
        return (count($this->data));
    }
    
    /**
     * Returns the current element.
     *
     * @return mixed the current element.
     */
    public function current()
    {
        return current($this->data);
    }
    
    /**
     * Returns the key for the current element.
     *
     * @return scalar the key of the current element.
     */
    public function key()
    {
        return key($this->data);
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->data)); 
    }
    
    /**
     * Rewind the iterator to the first element.
     *
     * @return void.
     */
    public function rewind()
    {
        $this->valid = (false !== reset($this->data));
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
