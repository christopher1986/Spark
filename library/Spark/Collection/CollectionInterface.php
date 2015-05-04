<?php

namespace Spark\Collection;

/**
 * An interface from which most collection types are derived. A collection represents a group of objects or values. Within the collection 
 * these objects or values are collectively known as it's elements. The CollectionInterface describes the minimal implementation for a 
 * collection type where subinterfaces such as the ListInterface define more specific behavior.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface CollectionInterface extends \Iterator, \Countable
{
    /**
     * Add the specified element to this collection if it not already present.
     *
     * @param mixed $element the element to add to this collection.
     * @return bool true if this collection did not already contain the specified element.
     */
    public function add($element);
    
    /**
     * Add to this collection all of the elements that are contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to add to this collection.
     * @return bool true if the collection has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function addAll($elements);
    
    /**
     * Removes all elements from this collection. The collection will be empty after this call returns.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Returns true if this collection contains the specified element. More formally returns true only if this collection
     * contains an element $e such that ($e === $element).
     *
     * @param mixed $element the element whose presence will be tested.
     * @return bool true if this collection contains the specified element, false otherwise.
     */
    public function contains($element);
    
    /**
     * Returns true if this collection contains all elements contained in the specified collection.
     *
     * @param array|\Traversable $elements collection of elements whose presence will be tested.
     * @return bool true if this collection contains all elements in the specified collection, false otherwise.
     */
    public function containsAll($elements);
    
    /**
     * Returns true if this collection is considered to be empty.
     *
     * @return bool true is this collection contains no elements, false otherwise.
     */
    public function isEmpty();
    
    /**
     * Removes the specified element from this collection if it is present. More formally removes an element $e
     * such that ($e === $element), if this collection contains such an element.
     *
     * @param mixed $element the element to remove from this collection.
     * @return mixed the element that was removed from the collection, or null if the element was not found.
     */
    public function remove($element);

    /**
     * Removes from this collection all of the elements that are contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to remove from this collection.
     * @return bool true if the collection has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @see CollectionInterface::remove($element)
     */
    public function removeAll($elements);
    
    /**
     * Retains only the elements in this collection that are contained in the specified collection. In other words,
     * remove from this collection all of it's elements that are not contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing element to be retained in this collection.
     * @return bool true if the collection has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function retainAll($elements);
    
    /**
     * Returns an array containing all elements in this collection. The caller is free to modify the returned
     * array since it has no reference to the actual elements contained by this collection.
     *
     * @return array an array containing all elements from this collection.
     */
    public function toArray();
}
