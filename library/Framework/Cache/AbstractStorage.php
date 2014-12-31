<?php

namespace Framework\Cache;

/**
 * A abstract storage from which other storages can be derived.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class AbstractStorage implements StorageInterface
{
    /** 
     * Retrieve an item.
     *
     * A CAS token will only be returned by storages that implement the {@link CheckAndSetCapableInterface} interface, 
     * otherwise the second argument if provided is simply ignored by this operation.
     *
     * @param string $key the key of the item to retrieve.
     * @param float $casToken if set will be populated with the CAS token.
     * @return mixed returns the value for the given key, or null if the item does not exist.
     */
    public function get($key, &$casToken = null)
    {
        // no permission to read from cache.
        if (!$this->getConfiguration()->isReadable()) {
            return false;
        }
        
        return $this->doGet($key, $casToken);
    }
    
    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        // no permission to read from cache.
        if (!$this->getConfiguration()->isReadable()) {
            return false;
        }
        
        return $this->doHas($key);
    }
    
    /**
     * {@inheritDoc}
     */
    public function add($key, $value)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return false;
        }
        
        return $this->doAdd($key, $value);
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return false;
        }
        
        return $this->doSet($key, $value);
    }
    
    /**
     * {@inheritDoc}
     */
    public function replace($key, $value)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return false;
        }
        
        return $this->doReplace($key, $value);
    }
     
    /**
     * {@inheritDoc}
     */
    public function increment($key, $offset = 1, $initial = 0)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return null;
        }
        
        return $this->doIncrement($key, $offset, $initial); 
    }
    
    /**
     * {@inheritDoc}
     */
    public function decrement($key, $offset = 1, $initial = 0)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return null;
        }
        
        return $this->doDecrement($key, $offset, $initial); 
    }
    
    /**
     * {@inheritDoc}
     */
    public function touch($key)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return false;
        }
        
        return $this->doTouch($key);
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return false;
        }
        
        return $this->doDelete($key);
    }
    
    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        // no permission to write to cache.
        if (!$this->getConfiguration()->isWritable()) {
            return;
        }
        
        $this->doFlush();
    }
    
    /**
     * Return a normalized key.
     *
     * @param  string $key the key to normalized.
     * @return string a normalized key.
     * @throws InvalidArgumentException if the key is empty or does not match the key pattern.
     */
    protected function normalizeKey($key)
    {
        $normalizedKey = (string) $key;

        if ($normalizedKey === '') {
            throw new \InvalidArgumentException('Storage does not support empty keys');
        } elseif (($pattern = $this->getConfiguration()->getKeyPattern()) && !preg_match($pattern, $normalizedKey)) {
            throw new \InvalidArgumentException(sprintf(
                'The key "%s" doesn\'t match with the key pattern "%s"',
                $normalizedKey,
                $pattern
            ));
        }
        
        return $normalizedKey;
    }
    
    /** 
     * Internal method to retrieve an item.
     *
     * A CAS token will only be returned by storages that implement the {@link CheckAndSetCapableInterface} interface, 
     * otherwise the second argument if provided is simply ignored by this operation.
     *
     * @param string $key the key of the item to retrieve.
     * @param float $casToken if set will be populated with the CAS token.
     * @return mixed returns the value for the given key, or null if the item does not exist.
     */
    protected abstract function doGet($key, &$casToken = null);
    
    /**
     * Internal method to test whether an item exists.
     *
     * @param string $key the key of an item whose presence will be tested.
     * @return bool true if an item exists, false otherwise.
     */
    protected abstract function doHas($key);
    
    /**
     * Internal method to add an item.
     *
     * @param string $key they key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    protected abstract function doAdd($key, $value);
    
    /**
     * Internal method to store an item.
     *
     * @param string $key they key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    protected abstract function doSet($key, $value);
    
    /**
     * Internal method to replace the item under an existing key.
     *
     * This method is similar to {@link StorageInterface::set($key, $value)}, but the operation will
     * fail if the key does not exist.
     *
     * @param string $Key the key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    protected abstract function doReplace($key, $value);
    
    /**
     * Internal method to increment a numeric item's value.
     *
     * This method will only increment numeric values. Passing anything else than a numeric value to this
     * method is considered a no-op, and will result in an error.
     *
     * @param string $key the key of the item to increment.
     * @param int $offset the amount by which to increment the item's value.
     * @param int $initial the value to set the item to if it doesn't currently exist.
     * @return mixed new item on succes, null on failure.
     */
    protected abstract function doIncrement($key, $offset = 1, $initial = 0);
    
    /**
     * Internal method to decrement a numeric item's value.
     *
     * This method will only decrement numeric values. Passing anything else than a numeric value to this
     * method is considered a no-op, and will result in an error.
     *
     * @param string $key the key of the item to decrement.
     * @param int $offset the amount by which to decrement the item's value.
     * @param int $initial the value to set the item to if it doesn't currently exist.
     * @return mixed new item on succes, null on failure.
     */
    protected abstract function doDecrement($key, $offset = 1, $initial = 0);
    
    /**
     * Internal method to reset expiration of an item.
     *
     * @param string $key the key of the item whose lifetime will be reset.
     * @return bool true if the lifetime was reset, false otherwise.
     */
    protected abstract function doTouch($key);
    
    /**
     * Internal method to delete an item.
     *
     * @param string $key the key of the item to delete.
     * return bool true on success, false on failure.
     */
    protected abstract function doDelete($key);
    
    /**
     * Internal method to invalidate all items.
     *
     * @return void.
     */
    protected abstract function doFlush();
}
