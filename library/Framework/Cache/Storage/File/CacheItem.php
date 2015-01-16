<?php

namespace Framework\Cache\Storage\File;

use SplFileObject;

/**
 * The CacheItem class offers an object oriented interface for a cache file.
 * 
 * @author Chris Harris
 * @link http://php.net/manual/en/class.splfileobject.php SplFileObject class
 */
class CacheItem extends SplFileObject implements CacheItemInterface
{
    /**
     * {@inheritDoc}
     */
    public function getExpirationTime()
    {
        $this->rewind();
        $lifetime = (int) $this->fgets();
        
        return $lifetime;
    }
    
    /**
     * {@inheritDoc}
     *
     * @link http://php.net/manual/en/class.splfileobject.php
     */
    public function getCacheData()
    {
        $this->rewind();
        $this->fgets();
        
        $data = '';
        while (!$this->eof()) {
            $data .= $this->fgets();
        }
        
        return $data;
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasExpired()
    {
        $lifetime = $this->getExpirationTime();
        return ($lifetime !== 0 && time() > $lifetime);
    }
}
