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

use Spark\Common\Equatable;

/**
 * This class implements the {@see ListInterface}, and is backed by a native array.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class ArrayList extends AbstractList
{
    /**
     * A native array to hold elements.
     *
     * @var array
     */
    private $items = array();

    /**
     * Internal pointer of the list.
     *
     * @var int
     */
    private $position = 0;

    /**
     * Construct a new ArrayList.
     *
     * @param mixed $elements one or more elements to add to the list, or null.
     */
    public function __construct($elements = null) 
    {
        if (null !== $elements) {
            $elements = (!is_array($elements) && !($elements instanceof \Traversable)) ? array($elements) : $elements;
            $this->addAll($elements);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($element, $index = -1)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index >= 0 && $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
    
        if ($index >= 0) {
            array_slice($this->items, $index, 0, (array) $element);
        } else {
            $this->items[] = $element;
        }

        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addAll($elements, $index = -1)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index >= 0 && $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        } else if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
    
        if ($elements instanceof \Traversable) {
            $elements = iterator_to_array($elements);
        }
    
        if ($index >= 0) {
            array_slice($this->items, $index, 0, $elements);
        } else {
            $this->items = array_merge($this->items, $elements);
        }
        
        return true;
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
    public function get($index)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($fromIndex) ? get_class($fromIndex) : gettype($fromIndex))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $retval = null;
        if (isset($this->items[$index])) {
            $retval = $this->items[$index];
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        $firstIndex = array_search($element, $this->items);
        if ($firstIndex === false) {
            $firstIndex = -1;
        }
        
        return $firstIndex;
    }
    
    /**
     * {@inheritDoc}
     */
    public function lastIndexOf($element)
    {
        $lastIndex = -1;
        if ($indices = array_keys($this->items, $element)) {
            $lastIndex = end($indices); 
        }
        
        return $lastIndex;
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        $retval = null;
        if (false !== ($index = array_search($element, $this->items))) {
            $retval = $this->removeByIndex($index);
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeByIndex($index)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($fromIndex) ? get_class($fromIndex) : gettype($fromIndex))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $retval = null;
        if (isset($this->items[$index])) {
            $retval = $this->items[$index];
            // remove element.
            unset($this->items[$index]);
            // reset array keys.
            $this->items = array_values($this->items);
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($index, $element)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $oldElement = null;
        if (isset($this->items[$index])) {
            $oldElement = $this->items[$index];
        }
        
        $this->items[$index] = $element;
        
        return $oldElement;
    }
    
    /**
     * {@inheritDoc}
     */
    public function subList($fromIndex, $toIndex)
    {
        if (!is_int($fromIndex) && $fromIndex < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a whole positive number; received "%s"',
                __METHOD__,
                $fromIndex
            ));
        } else if (!is_int($toIndex) && $toIndex < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a whole positive number; received "%s"',
                __METHOD__,
                $toIndex
            ));
        } else if ($fromIndex > $toIndex) {
            throw new \LogicException(sprintf(
                '%s: $fromIndex(%s) > $toIndex(%s)',
                __METHOD__,
                $fromIndex,
                $toIndex
            ));
        } else if ($fromIndex < 0) {
            throw new \LogicException(sprintf(
                '%s: $fromIndex(%s) cannot be smaller than 0',
                __METHOD__,
                $fromIndex
            ));
        } else if ($toIndex > $this->count()) {
            throw new \LogicException(sprintf(
                '%s: $toIndex(%s) cannot be larger than %d',
                __METHOD__,
                $toIndex,
                $this->count()
            ));
        }
        
        $offset = $fromIndex;
        $length = $toIndex - $fromIndex;
        
        $list = new ArrayList();
        if ($elements = array_slice($this->toArray(), $offset, $length)) {
            $list->addAll($elements);
        }
        return $list;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->items;
    }
    
    /**
     * Returns the number of elements contained by this list.
     *
     * @return int the number of elements contained by this list.
     */
    public function count()
    {
        return count($this->items);
    }
    
    /**
     * Returns the current element.
     *
     * @return mixed the current element.
     */
    public function current()
    {
        return $this->items[$this->position];
    }
    
    /**
     * Returns the index for the current element.
     *
     * @return int the index of the current element.
     */
    public function key()
    {
        return $this->position;
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }
    
    /**
     * Rewind iterator to the first element.
     */
    public function rewind()
    {
        $this->position = 0;
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return bool true if the current position is valid, false otherwise.
     */
    public function valid()
    {
        return (isset($this->items[$this->position]));
    }
}
