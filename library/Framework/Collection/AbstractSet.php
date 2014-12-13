<?php

namespace Framework\Collection;

/**
 * This class provides a partial implementation of the {@see SetInterface}
 * to minimize the effort required to implement this interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class AbstractSet implements SetInterface
{
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
    public function addAll($elements)
    {
        if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
        
        $modified = false;
        foreach ($elements as $element) {
            if ($this->add($element)) {        
                $modified = true;
            }
        }
        return $modified;
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
                if ($this->remove($element)) {
                    $modified = true;
                }
            }
        } else {
            // iterate over (copy) array to prevent non-deterministic behavior.
            $tmp = $this->toArray();
            foreach ($tmp as $element) {
                if (in_array($element, $elements) && $this->remove($element)) {
                    $modified = true;
                }
            }
        }
        
        return $modified;
    }
}
