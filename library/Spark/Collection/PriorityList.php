<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Collection;

use Spark\Common\Comparator\ComparableComparator;
use Spark\Common\Comparator\CompositeComparator;
use Spark\Common\Comparator\NumericComparator;
use Spark\Common\Comparator\StringComparator;
use Spark\Common\Equatable;
use Spark\Util\Arrays;

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
     * @var CompositeComparator
     */
    private $comparator;
    
    /**
     * A flag to determine if the list is stored.
     *
     * @var bool
     */
    private $sorted = true;
    
    /**
     * Construct a PriorityList.
     *
     * @param mixed $elements one or more elements to add to the list, or null.
     */
    public function __construct($elements = null)
    {
        $this->comparator = $this->createComparator();
        
        if (null !== $elements) {
            $elements = (!is_array($elements) && !($elements instanceof \Traversable)) ? array($elements) : $elements;
            $this->addAll($elements);
        }
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
        
        // previous number of element in the list.
        $oldSize = count($this->items);
        // append all elements to the list.
        $this->items = array_merge($this->items, $elements);
        // determine if list is invalid
        $this->sorted = ($oldSize == count($this->items));
        
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
