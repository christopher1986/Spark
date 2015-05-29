<?php

namespace Spark\Db\Sql;

use Countable;
use IteratorAggregate;

interface CompositeInterface extends Countable, IteratorAggregate
{   
    /**
     * Add a collection of elements.
     *
     * @param array|Traversable a collection of elements.
     * @throws \InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function addAll($elements);
    
    /**
     * Removes all elements from this composite. The composite will be empty after this call returns.
     *
     * @return void.
     */
    public function clear();
    
    /**
     * Returns true if this composite contains no elements.
     *
     * @return true if this composite is empty, false otherwise.
     */
    public function isEmpty();
}
