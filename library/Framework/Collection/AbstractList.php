<?php

namespace Framework\Collection;

/**
 * This class provides a partial implementation of the {@see ListInterface}
 * to minimize the effort required to implement this interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class AbstractList implements ListInterface
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
    
        foreach ($elements as $element) {
            $this->add($element, $index);
            if ($index >= 0) {
                $index++;     
            }
        }
        
        return true;
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
                if (null !== $this->remove($element)) {
                    $modified = true;
                }
            }
        } else {
            // iterate over (copy) array to prevent non-deterministic behavior.
            $data = $this->toArray();
            foreach ($data as $element) {
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
        // use a (copy) array to prevent non-deterministic behavior.
        $tmp = $this->toArray();
        foreach ($tmp as $index => $element) {
            if (!in_array($element, $elements)) {
                if (null !== $this->removeByIndex($index)) {
                    $modified = true;
                }
            }
        }
        
        return $modified;
    }
}
