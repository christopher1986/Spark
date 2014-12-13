<?php

namespace Framework\Sort\Comparator;

/**
 * A comparator that is capable of ordering strings.
 *
 * @author Chris Harris
 * @verson 1.0.0
 */
class StringComparator extends AbstractComparator
{
    /**
     * {@inheritDoc}
     */
    public function accepts($firstValue, $secondValue)
    {
        return (is_string($firstValue) && is_string($secondValue));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function internalCompare($firstValue, $secondValue)
    {
        return strcmp($firstValue, $secondValue);
    }


}
