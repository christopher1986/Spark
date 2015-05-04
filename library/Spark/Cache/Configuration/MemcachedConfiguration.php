<?php

namespace Spark\Cache\Configuration;

use Spark\Cache\Memcached\Storage\ServerSet;

/**
 * The MemcachedConfiguration is a configuration that is specifically designed to be used with memcache.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class MemcachedConfiguration extends Configuration
{
    /**
     * A unique ID for a Memcached instance.
     *
     * @var string
     */
    private $persistentId;

    /**
     * A collection containing Server objects.
     *
     * @var ServerSet
     */
    private $servers;
    
    /**
     * An array containing key-value pairs of Memcached options.
     *
     * @var array
     */
    private $options = array();
    
    /**
     * Set a string that will be prepended to the key of an item.
     *
     * Maximum size of a memcache key is limited to 250 bytes, meaning that the prefix is
     * limited to 100 bytes. This allows a key to be at most 150 bytes.
     *
     * @param string $prefix a string to prepend to a key.
     * @throws InvalidArgumentException if the given argument if not of type string.
     * @throws InvalidArgumentException if the given string is larger than 100 bytes.
     */
    public function setPrefix($prefix)
    {
	    if (!is_string($prefix)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($prefix) ? get_class($prefix) : gettype($prefix))
            ));
	    } else if (($length = strlen($prefix)) > 100) {
            throw new \InvalidArgumentException(sprintf(
                '%s: string should not exceed 100 bytes; received "%d" bytes',
                __METHOD__,
                $length
            ));
	    }
	    
	    $this->prefix = $prefix;
    }
    
    /**
     * Set a persistent id.
     *
     * @param string $persistentId a unique id to persist a Memcached instance between request.
     * @see MemcachedConfiguration::getPersistentId()
     */
    public function setPersistentId($persistentId)
    {
        if (!is_string($persistentId)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($persistentId) ? get_class($persistentId) : gettype($persistentId))
            ));
	    }
	    
	    $this->persistentId = $persistentId;
    }
    
    /**
     * Returns the persistent id.
     *
     * By default Memcached instances are destroyed at the end of the request. A persistent id 
     * allows an instance to persist between requests.
     *
     * @return string|null the persistent id if present, otherwise null.
     * @link http://php.net/manual/en/memcached.construct.php
     */
    public function getPersistentId()
    {
        return $this->persistentId;
    }
    
    /**
     * Set a collection containing information about one or more memcache servers.
     *
     * @param array|Traversable $servers a collection that contains information about one or more memcache servers.
     */
    public function setServers($servers) 
    {
        if (!is_array($servers) && !($servers instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($servers) ? get_class($servers) : gettype($servers))
            ));
        }
        
        $this->getServers()->addAll($servers);
    }
    
    /**
     * Returns a collection of Server objects.
     *
     * @return ServerSet a collection of Server objects.
     */
    public function getServers()
    {
        if ($this->servers === null) {
            $this->servers = new ServerSet();
        }
        return $this->servers;
    }
    
    /**
     * Set Memcached options.
     *
     * @param array $options associative array of options where the key is the option to set and the value
     *                       is the new value for the options.
     * @link http://php.net/manual/en/memcached.setoptions.php
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
    
    /**
     * Returns the Memcached options.
     *
     * @return array the Memcached options.
     * @see MemcachedOptions::setOption($options)
     */
    public function getOptions()
    {
        return $this->options;
    }
}
