<?php

namespace Framework\Sort\Comparator;

use Framework\Sort\Comparable;

/**
 * A comparator that is capable of ordering objects that implement the Comparable interface.
 *
 * @author Chris Harris
 * @verson 1.0.0
 */
class ComparableComparator extends AbstractComparator
{
    /**
     * {@inheritDoc}
     */
    public function accepts($firstObj, $secondObj)
    {
        return (is_object($firstObj) && is_object($secondObj)
                    && (get_class($firstObj) == get_class($secondObj))
                    && ($firstObj instanceof Comparable));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function internalCompare($firstObj, $secondObj)
    {
        return $firstObj->compareTo($secondObj);
    }
}
