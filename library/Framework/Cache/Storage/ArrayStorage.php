<?php

namespace Framework\Cache\Storage;

use Framework\Cache\ConfigurableInterface;
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
class ArrayStorage extends AbstractStorage implements ConfigurableInterface
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
     * Create an ArrayStorage.
     *
     * @param array|Traversable|ConfigurationInterface|null $config configuration for this storage, or null to use the default configuration.
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->setConfiguration($config);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function doGet($key, &$casToken = null)
    {
        $value = null;
        if ($this->has($key)) {
            $internalKey = $this->getInternalKey($key);
            $value = $this->items[$internalKey];
        }
        
        return $value;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doHas($key)
    {
        $internalKey = $this->getInternalKey($key);
        return (isset($this->items[$internalKey]));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doAdd($key, $value)
    {
        $added = false;
        if ($added = !$this->has($key)) {
            $internalKey = $this->getInternalKey($key);
            $this->items[$internalKey] = $value;
        }
        
        return $added;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doSet($key, $value)
    {
        $internalKey = $this->getInternalKey($key);
        $this->items[$internalKey] = $value;
        
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
        $newValue = null;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue + $offset);
                $this->set($key, $newValue);
            }
        } else {
            $newValue = (int) $initial;
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDecrement($key, $offset = 1, $initial = 0)
    {    
        $newValue = null;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue - $offset);
                $this->set($key, $newValue);
            }
        } else {
            $newValue = $initial;
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDelete($key)
    {
        $internalKey = $this->getInternalKey($key);
    
        $deleted = false;
        if ($deleted = $this->has($internalKey)) {
            unset($this->items[$internalKey]);
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
     * Returns the configuration used by this storage.
     *
     * @return Configuration a configuration object.
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
     */
    protected function normalizeKey($key)
    {
        // normalize first uppercase character.
        $letters = function($letters) {
            return sprintf('-%s', strtolower(trim($letters[1])));
        };
        
        $normalizedKey = parent::normalizeKey($key);
        $prefix = $this->getConfiguration()->getPrefix();
        if (strlen($prefix) > 0) {
            $normalizedKey = sprintf('%s-%s', $prefix, $normalizedKey);
        }
        
        return preg_replace_callback('#([A-Z\s]+)#', $letters, Strings::lcfirst($key));
    }
}
