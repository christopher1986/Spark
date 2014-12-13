<?php

namespace Framework\Collection;

use Framework\Sort\Comparator\ComparableComparator;
use Framework\Sort\Comparator\CompositeComparator;
use Framework\Sort\Comparator\NumericComparator;
use Framework\Sort\Comparator\StringComparator;
use Framework\Util\Arrays;

class PriorityList implements PriorityListInterface
{
    /**
     * A native array to hold elements.
     *
     * @var array
     */
    private $items = array();

    /**
     * A composite or comparators to order this list.
     *
     * @type CompositeComparator
     */
    private $comparator;
    
    /**
     * Construct a PriorityList.
     */
    public function __construct()
    {
        $this->comparator = $this->createComparator();
    }
    
    /**
     * {@inheritDoc}
     */
    public function add($element)
    {    
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: does not allow NULL elements; received "%s"',
                __METHOD__,
                (is_object($element) ? get_class($element) : gettype($element))
            ));
        }
    
        // add element to the list.
        $this->items[] = $element;
        // invalidate sorted.
        $this->sorted = false;

        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addAll($elements)
    {
        if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
    
        if ($elements instanceof \Traversable) {
            $elements = iterator_to_array($elements);
        }
        

        
        // append all elements to the list.
        $this->items = array_merge($this->items, $elements);
        // invalidate sorted.
        $this->sorted = false;
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->items = array();
        // invalidate sorted.
        $this->sorted = false;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return (in_array($element, $this->items));
    }
    
    /**
     * {@inheritDoc}
     */
    public function containsAll($elements)
    {
        if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        } 
    
        foreach ($elements as $element) {
            if (!$this->contains($element)) {
                return false;
            }           
        }
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        $retval = null;
        if (false !== ($index = array_search($element, $this->items))) {
            $retval = $this->items[$index];
            // remove element.
            unset($this->items[$index]);
            // invalidate sorted.
            $this->sorted = false;
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeAll($elements)
    {
        if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }

        if ($elements instanceof \Traversable) {
            $elements = iterator_to_array($elements);
        }
        
        $modified = false;
        if ($this->count() > count($elements)) {
            foreach ($elements as $element) {
                if (null !== $this->remove($element)) {
                    $modified = true;
                }
            }
        } else {
            // iterate over (copy) array to prevent non-deterministic behavior.
            $items = $this->toArray();
            foreach ($items as $element) {
                if (in_array($element, $elements) && (null !== $this->remove($element))) {
                    $modified = true;
                }
            }
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */ 
    public function retainAll($elements)
    {
        if (!is_array($elements) && !$elements instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable as argument; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
        
        $modified = false;
        // iterate over (copy) array to prevent non-deterministic behavior.
        $tmp = $this->toArray();
        foreach ($tmp as $index => $element) {
            if (!in_array($element, $elements)) {
                if (null !== $this->remove($element)) {
                    $modified = true;
                }
            }
        }
        
        return $modified;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        // sort array first.
        Arrays::sort($this->items, $this->getComparator());
    
        return $this->items;
    }
    
    /**
     * Returns the number of elements contained by this list.
     *
     * @return int the number of elements contained by this list.
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
     * Rewind iterator to the first element.
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        if (!$this->sorted) {
            Arrays::sort($this->items, $this->getComparator());
            // validate sorted. 
            $this->sorted = true;
        }    
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
    
    /**
     * Returns the comparator used to order elements in this list.
     *
     * @return CompositeComparator a comparator used for ordering of elements in this list.
     */
    public function getComparator()
    {        
        return $this->comparator;
    }
    
    /**
     * Returns a default comparators used to order the priority list.
     *
     * @return void
     */
    private function createComparator()
    { 
        $comparators = array();
        $comparators[] = new ComparableComparator();
        $comparators[] = new NumericComparator();
        $comparators[] = new StringComparator();
        
        return new CompositeComparator($comparators);
    }
}
