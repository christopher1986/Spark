<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Framework\Cache\Configuration;

/**
 * A configuration that implements the {@link ConfigurationInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Determines if cache is readable.
     *
     * @var bool
     */
    private $readable = true;

    /**
     * Determines if cache is writable.
     *
     * @var bool
     */
    private $writable = true;
    
    /**
     * A pattern that keys must match.
     *
     * @string
     */
    private $keyPattern = '';
   
    /**
     * The time to live in seconds.
     *
     * @var int
     */
    private $timeToLive = 0;
    
    /**
     * The namespace.
     *
     * @var string
     */
    private $namespace = '';    

    /**
     * Create configuration.
     *
     * @param array|
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
    }
    
    /**
     * A collection of options to populate the configuration with.
     *
     * @param array|Traversable|ConfigurationInterface $options options to use for this configuration.
     */
    private function setOptions($options)
    {
        if ($options instanceof self) {
            $options = $options->toArray();
        }
        
        if (!is_array($options) && !($options instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            $setterMethod = sprintf('set%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if (method_exists($this, $setterMethod)) {
                $this->{$setterMethod}($value);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function isReadable()
    {
        return $this->readable;
    }
    
    /**
     * Determines whether reading from the cache is allowed.
     *
     * @param bool $readable allow or disallow reading from the cache.
     */
    public function setReadable($readable)
    {
        $this->readable = (bool) $readable;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isWritable()
    {
        return $this->writable;
    }
    
    /**
     * Determines whether writing to the cache is allowed.
     *
     * @param bool $writable allow or disallow writing to the cache.
     */
    public function setWritable($writable)
    {
        $this->writable = (bool) $writable;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getKeyPattern()
    {
        return $this->keyPattern;
    }
    
    /**
     * Set a pattern that all keys must match.
     *
     * @param string $keyPattern a pattern to match.
     * @throws InvalidArgumentException if the given argument is not of type string.
     */
    public function setKeyPattern($keyPattern)
    {
	    if (!is_string($keyPattern)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($keyPattern) ? get_class($keyPattern) : gettype($keyPattern))
            ));
	    }
	    
	    $this->keyPattern = $keyPattern;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }
    
    /**
     * Set the expiration time in seconds before an item is invalidated.
     *
     * @param int $ttl time in seconds an item should remain in the cache.
     * @throws InvalidArgumentException if the given argument is not numeric.
     */
    public function setTimeToLive($ttl = 0)
    {
	    if (!is_numeric($ttl)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($ttl) ? get_class($ttl) : gettype($ttl))
            ));
	    } 
	    
	    $this->timeToLive = (int) $ttl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    /**
     * Set the namespace that some storages will prepend to the key of an item.
     *
     * @param string $namespace a namespace to prepent to a key.
     * @throws InvalidArgumentException if the given argument if not of type string.
     */
    public function setNamespace($namespace)
    {
	    if (!is_string($namespace)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($namespace) ? get_class($namespace) : gettype($namespace))
            ));
	    }
	    
	    $this->namespace = $namespace;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {        
        // normalize key so it starts with an underscore and lowercase letter.
        $normalize = function ($letters) {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };
        
        $retval = array();
        foreach ($this as $property => $value) {
            $key = preg_replace_callback('/([A-Z])/', $normalize, $property);
            $getterMethod = sprintf('get%s', ucfirst($property));
            if (method_exists($this, $getterMethod)) {
                $retval[$key] = $this->{$getterMethod}();
            } else {
                $retval[$key] = $value;
            }
        }
        
        return $retval;
    }
}
