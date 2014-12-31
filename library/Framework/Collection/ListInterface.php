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

namespace Framework\Collection;

/**
 * An ordered collection (also known as a sequence). Elements can be inserted to the list at
 * specified positions. These elements can be retrieved from the list using a (positive integer)
 * position.
 *
 * @author Chris Harris
 * @version 1.0.0.
 */
interface ListInterface extends \Iterator, \Countable
{
    /**
     * Add the specified element to this list if it not already present.
     *
     * @param mixed $element the element to add to this list.
     * @param int $index optional index to insert the element at the specified position.
     * @return bool true if the element was added to the list.
     * @throws \InvalidArgumentException if the $index argument is not a numeric value.
     */
    public function add($element, $index = -1);
    
    /**
     * Add to this list all of the elements that are contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to add to this list.
     * @param int $index optional index to insert the elements at the specified position.
     * @return bool true if the list has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not a numeric value.
     * @throws \OutOfRangeException if the index is out of range ($index < 0 || $index >= List::count()).
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function addAll($elements, $index = -1);
    
    /**
     * Removes all elements from this list. The list will be empty after this call returns.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Returns true if this list contains the specified element. More formally returns true only if this list
     * contains an element $e such that ($e === $element).
     *
     * @param mixed $element the element whose presence will be tested.
     * @return bool true if this list contains the specified element, false otherwise.
     */
    public function contains($element);
    
    /**
     * Returns true if this list contains all elements contained in the specified collection.
     *
     * @param array|\Traversable $elements collection elements whose presence will be tested.
     * @return bool true if this list contains all elements in the specified collection, false otherwise.
     * @see ListInterface::contains($element)
     */
    public function containsAll($elements);
    
    /**
     * Returns the element at the specified position in the list.
     *
     * @param int $index index of the element to return.
     * @return mixed the element at the specified position in this list, or null.
     * @throws \InvalidArgumentException if the given argument is not a numeric value.
     * @throws \OutOfRangeException if the index is out of range ($index < 0 || $index >= List::count()).
     */
    public function get($index);
    
    /**
     * Returns the index of the first occurence of the specified element in this list, or -1 if this list
     * does not contain the element.
     *
     * @param mixed $element to element to search for.
     * @return int the index of the first occurence of the specified element in this list, or -1 if this list
     *             does not contain the element.
     */
    public function indexOf($element);
    
    /**
     * Returns the index of the last occurence of the specified element in this list, or -1 if this list
     * does not contain the element.
     *
     * @param mixed $element to element to search for.
     * @return int the index of the last occurence of the specified element in this list, or -1 if this list
     *             does not contain the element.
     */
    public function lastIndexOf($element);
    
    /**
     * Returns true if this list is considered to be empty.
     *
     * @return bool true is this list contains no elements, false otherwise.
     */
    public function isEmpty();
    
    /**
     * Removes the specified element from this list if it is present. More formally removes an element $e
     * such that ($e === $element), if this list contains such an element.
     *
     * @param mixed $element the element to remove from this list.
     * @return mixed the element that was removed from the list, or null if the element was not found.
     */
    public function remove($element);
    
    /**
     * Removes the specified element from this list if it is present. More formally removes an element $e
     * such that ($e === $element), if this list contains such an element.
     *
     * @param int index the index of the element to be removed.
     * @return mixed the element that was removed from the list, or null if no element was not found.
     * @throws \InvalidArgumentException if the given argument is not a numeric value.
     * @throws \OutOfRangeException if the index is out of range ($index < 0 || $index >= List::count()).
     */
    public function removeByIndex($index);
    
    /**
     * Removes from this list all of the elements that are contained in the specified collection.
     *
     * This implementation determines which if this list of the given collection is smaller, by invoking
     * the {@link count()} method on each. If this list has fewer elements, then the implementation iterates
     * over this list, checking each element to see if it is contained in the specified collection. If the
     * specified collection has fewer elements, then the implementation iterates of the specified collection,
     * removing from this list each element contained by the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to remove from this list.
     * @return bool true if the list has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @see ListInterface::remove($element)
     */
    public function removeAll($elements);
    
    /**
     * Retains only the elements in this list that are contained in the specified collection. In other words,
     * remove from this list all of it's elements that are not contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing element to be retained in this list.
     * @return bool true if the list has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function retainAll($elements);
    
    /**
     * Insert the specified element at the specified position in this list. Elements after the specified position
     * will be shifted to the right to accommodate for the newly inserted element.
     *
     * @param int $index a position integer position, also known as an index.
     * @param mixed $element the element to be inserted.
     * @return mixed the element previously at the specified position.
     * @throws \InvalidArgumentException if the given argument is not a numeric value.
     * @throws \OutOfRangeException if the index is out of range ($index < 0 || $index >= List::count()).
     */
    public function set($index, $element);
    
    /**
     * Returns a portion of this list between the specified $fromIndex, inclusive, and $toIndex, exclusive.
     * The caller is free to modify the returned list since it has no reference to the actual element contained
     * by this list.
     *
     * @param int $fromIndex the index of the first element to include in the list.
     * @param int $toIndex the index of the last element which will not be included in the list.
     * @return List a list containing the elements between the specified two indexes.
     * @throws \InvalidArgumentException if either of the indexes are not positive numbers.
     * @throws \LogicException if $toIndex is larger than $fromIndex;
     * @throws \LogicException if $fromIndex is smaller than 0;
     * @throws \LogicException if $toIndex is larger than the number of elements contained by this list;
     */
    public function subList($fromIndex, $toIndex);
    
    /**
     * Returns an array containing all elements in this list. The caller is free to modify the returned
     * array since it has no reference to the actual elements contained by this list.
     *
     * @return array an array containing all elements from this list.
     */
    public function toArray();
}
