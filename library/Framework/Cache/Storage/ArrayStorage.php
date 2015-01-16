<?php

namespace Framework\Cache\Storage;

use Framework\Cache\Configuration\ConfigurationInterface;
use Framework\Cache\Configuration\Configuration;
use Framework\Util\Strings;

/**
 * A storage that is capable of storing items in an array. Unlike other storages the items contained by the ArrayStorage do not have a
 * lifetime that can be set. This is because items in the ArrayStorage only exist for a single page request. After PHP has interpreted 
 * and executed the code all items within this storage are garbage collected once PHP releases it's memory.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class ArrayStorage extends AbstractStorage
{
    /**
     * A configuration object for the storage.
     *
     * @var ConfigurationInterface
     */
    private $config;
    
    /**
     * A collection of items.
     *
     * @var array
     */
    private $items = array();

    /**
     * {@inheritDoc}
     */
    protected function doGet($key, &$casToken = null)
    {
        $value = null;
        if ($this->has($key)) {
            $normalizedKey = $this->normalizeKey($key);
            $value = $this->items[$normalizedKey];
        }
        
        return $value;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doHas($key)
    {
        $normalizedKey = $this->normalizeKey($key);
        return (isset($this->items[$normalizedKey]));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doAdd($key, $value)
    {
        $added = false;
        if ($added = !$this->has($key)) {
            $normalizedKey = $this->normalizeKey($key);
            $this->items[$normalizedKey] = $value;
        }
        
        return $added;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doSet($key, $value)
    {
        $normalizedKey = $this->normalizeKey($key);
        $this->items[$normalizedKey] = $value;
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doReplace($key, $value)
    {
        $replaced = false;
        if ($replaced = $this->has($key)) {
            $this->set($key, $value);
        }
        
        return $replaced;
    }
     
    /**
     * {@inheritDoc}
     */
    protected function doIncrement($key, $offset = 1, $initial = 0)
    {    
        $newValue = (int) $initial;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue + $offset);
            }
            $this->set($key, $newValue);
        } else {
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDecrement($key, $offset = 1, $initial = 0)
    {    
        $newValue = (int) $initial;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue - $offset);
                $newValue = ($newValue < 0) ? 0 : $newValue;
            }
            $this->set($key, $newValue);
        } else {
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDelete($key)
    {    
        $deleted = false;
        if ($deleted = $this->has($key)) {
            $normalizedKey = $this->normalizeKey($key);
            unset($this->items[$normalizedKey]);
        }
        
        return $deleted;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doFlush()
    {
        $this->items = array();
        return true;
    }
    
    
    /**
     * Time to live for items is not supported, so calling this method is a no-op.
     *
     * @return bool returns true since items in this storage have no expiration time.
     */
    protected function doTouch($key)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfiguration($config)
    {
        if (is_array($config) || $config instanceof \Traversable) {
            $config = new Configuration($config);
        }
        
        if (!($config instanceof ConfigurationInterface)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an object that implements "ConfigurationInterface"; received "%s"',
                __METHOD__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }
    
        $this->config = $config;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        if ($this->config === null) {
            $this->setConfiguration(new Configuration());
        }
        
        return $this->config;
    }
    
    /**
     * {@inheritDoc}
     *
     * The key returned by this method consists of a normalized key and an optional prefix
     * that is prepended to the already normalized key.
     *
     * @param  string $key the key to normalized.
     * @return string a normalized key.
     * @throws InvalidArgumentException if the key is empty or does not match the key pattern.
     */
    public function normalizeKey($key)
    {
        $normalizedKey = parent::normalizeKey($key);

        // normalize first uppercase character.
        $letters = function($letters) {
            return sprintf('-%s', strtolower(trim($letters[1])));
        };
        
        // concatenate prefix with normalized key.
        if (($prefix = $this->getConfiguration()->getPrefix()) !== '') {
            $normalizedKey = sprintf('%s-%s', $prefix, $normalizedKey);
        }
        
        return preg_replace_callback('#([A-Z\s]+)#', $letters, Strings::lcfirst($normalizedKey));
    }
}
