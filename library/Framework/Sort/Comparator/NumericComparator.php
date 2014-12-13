<?php

namespace Framework\Sort\Comparator;

/**
 * A comparator that is capable of ordering numeric values.
 *
 * @author Chris Harris
 * @verson 1.0.0
 */
class NumericComparator extends AbstractComparator
{
    /**
     * {@inheritDoc}
     */
    public function accepts($firstValue, $secondValue)
    {
        return (is_numeric($firstValue) && is_numeric($secondValue));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function internalCompare($firstValue, $secondValue)
    {
        if ($firstValue == $secondValue) {
            return 0;
        }        
        return ($firstValue > $secondValue) ? 1 : -1;
    }


}
