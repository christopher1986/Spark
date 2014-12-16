<?php

namespace Framework\Collection;

use Framework\Common\Equatable;

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
    private $items = array();

    /**
     * Indicate if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;
    
    /**
     * Construct a new Set.
     *
     * @param mixed $items one or more elements to add to the set, or null.
     */
    public function __construct($items = null) 
    {
        if (null !== $items) {
            $elements = (!is_array($items) && !($items instanceof \Traversable)) ? array($items) : $items;
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
            $this->items[] = $element;
            $modified = true;
        }
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        $contains = false;
        if ($element instanceof Equatable) {
            foreach ($this->items as $item) {
                if ($contains = $element->equals($item)) {
                    break;
                }
            }
        } else {
            $contains = in_array($element, $this->items);
        }
        return $contains;
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        $modified = false;
        if (false !== ($index = array_search($element, $this->items))) {
            unset($this->items[$index]);
            $modified = true;
        }
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->items = array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->items;
    }
    
    /**
     * Returns the number of elements contained by this set.
     *
     * @return int the number of elements contained by this set.
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
