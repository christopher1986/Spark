<?php 

namespace Framework\Cache\Storage;

use Memcached;

use Framework\Cache\AvailableSpaceCapableInterface;
use Framework\Cache\CheckAndSetCapableInterface;
use Framework\Cache\ResultCodeCapableInterface;
use Framework\Cache\ResultMessageCapableInterface;
use Framework\Cache\TotalSpaceCapableInterface;
use Framework\Cache\Configuration\MemcachedConfiguration;
use Framework\Cache\Memcached\Storage\ServerSet;

/**
 * A storage class that supports the storage of values through a general-purpose distributed memory caching system known as Memcached.
 * 
 * A Memcached server typically stores values into the RAM which allows any value given to this storage to be available for prolonged
 * use. A server will keep values in RAM until the lifetime of that value has expired or if the server runs out of RAM, in which case 
 * it will discard the oldest values. Therefore, clients must treat Memcached as a transitory cache; they cannot assume that data stored 
 * in Memcached is still there when they need it. 
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class MemcachedStorage extends AbstractStorage implements AvailableSpaceCapableInterface, CheckAndSetCapableInterface, ResultCodeCapableInterface, ResultMessageCapableInterface, TotalSpaceCapableInterface
{
    /**
     * A Memcached instance on which this storage operates.
     *
     * @var Memcached
     */
    private $memcached;

    /**
     * {@inheritDoc}
     */
    protected function doGet($key, &$casToken = null)
    {
        $normalizedKey = $this->normalizeKey($key);
        return $this->getMemcached()->get($normalizedKey, $casToken);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doHas($key)
    {
        $value = $this->get($key);
        if ($value === false || $value === null) {
            $resultCode = $this->getResultCode();
            if ($resultCode === Memcached::RES_NOTFOUND) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doAdd($key, $value)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        return $this->getMemcached()->add($normalizedKey, $value, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doSet($key, $value)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        return $this->getMemcached()->set($normalizedKey, $value, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doReplace($key, $value)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        return $this->getMemcached()->replace($normalizedKey, $value, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doIncrement($key, $offset = 1, $initial = 0)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        
        return $this->getMemcached->increment($normalizedKey, (int) $offset, (int) $initial, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDecrement($key, $offset = 1, $initial = 0)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        
        return $this->getMemcached->decrement($normalizedKey, (int) $offset, (int) $initial, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doTouch($key)
    {
        $normalizedKey = $this->normalizeKey($key);
        $expiration  = $this->getExpirationTime();
        
        return $this->getMemcached->touch($normalizedKey, $expiration);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDelete($key)
    {
        $normalizedKey  = $this->normalizeKey($key);
                    
        return $this->getMemcached->delete($normalizedKey);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doFlush()
    {
        return $this->getMemcached()->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function cas($casToken, $key, $value)
    {
        return $this->getMemcached->cas($casToken, $key, $value);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setConfiguration($config)
    {
        if (is_array($config) || $config instanceof \Traversable) {
            $config = new MemcachedConfiguration($config);
        }
        
        if (!($config instanceof MemcachedConfiguration)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a "MemcachedConfiguration" object; received "%s"',
                __METHOD__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }
        
        // only invalidate if a new config is provided.
        $oldConfig = $this->config;
        if ($oldConfig === $config) {
            return;
        }
        
        $this->config = $config;
        $this->invalidateMemcache();    
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        if ($this->config === null) {
            $this->setConfiguration(new MemcachedConfiguration());
        }
        
        return $this->config;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResultCode()
    {
        return $this->getMemcached()->getResultCode();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResultMessage()
    {
        return $this->getMemcached()->getResultMessage();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTotalSpace()
    {    
        $totalSpace = 0;
        $stats = $this->getMemcached()->getStats();
        if (is_array($stats)) {
            foreach ($stats as $stat) {
                $totalSpace += $stat['limit_maxbytes'];
            }
        }
        
        return $totalSpace;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAvailableSpace()
    {    
        $availableSpace = $this->getTotalSpace();
        $stats = $this->getMemcached()->getStats();
        if (is_array($stats)) {
            foreach ($stats as $stat) {
                $availableSpace -= $stat['bytes'];
            }
        }
        
        return $availableSpace;
    }
    
    /**
     * Returns the expiration time of an item.
     *
     * An expiration value that exceeds 60*60*24*30 (number of seconds in 30 days) will be treated as 
     * a Unix timestamp. So if the configuration for this storage contains a time to live that exceeds 
     * the 30 day boundary it's value will be added to the current Unix timestamp.
     *
     * @return int the expiration of an item.
     * @link http://php.net/manual/en/memcached.expiration.php
     */
    public function getExpirationTime()
    {
        // time to live provided by the configuration.
        $timeToLive = $this->getConfiguration()->getTimeToLive();
        // maximum time to live expressed in seconds (30 days).
        $maxTimeToLive = 2592000;

        if ($timeToLive > $maxTimeToLive) {
            return (time() + $timeToLive);
        }
        return $timeToLive;
    }
    
    /**
     * Returns the Memcached instance on which this storage operates.
     *
     * @return Memcached a Memcached object.
     */
    private function getMemcached()
    {
        // storage is in invalid state.
        if ($this->memcached === null) {
            $this->invalidateMemcache();
        }
        return $this->memcached;
    }
    
    /**
     * Invalidate this Memcached storage.
     *
     * By invalidating this storage a new Memcached instance is created, and any previously created
     * Memcached instance if replaced by the new one.
     */
    private function invalidateMemcache()
    {
        $config = $this->getConfiguration();
        
        if (($persistentId = $config->getPersistentId()) !== null) {
            $memcached = new Memcached($persistentId);
        } else {
            $memcached = new Memcached();
        }

        if (method_exists($memcached, 'setOptions')) {
            $memcached->setOptions($config->getOptions());
        } else {
            $options = $config->getOptions();
            foreach ($options as $key => $value) {
                $memcached->setOption($key, $value);
            }
        }

        $servers = $config->getServers();
        if (!$servers->isEmpty()) {
            foreach ($servers as $server) {
                $memcached->addServer($server->getHost(), $server->getPort(), $server->getWeight());  
            }
        }
        
        $this->memcached = $memcached;
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
