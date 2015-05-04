<?php

namespace Spark\Cache\Storage\File;

use SplFileObject;

/**
 * The CacheItemInterface describes a cache item stored on the filesystem.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
interface CacheItemInterface
{
    /**
     * Returns the expiration date as a Unix timestamp.
     *
     * You can use {@see date($format, $timestamp)} or the {@see DateTime} class 
     * if you wish to display the expiration in some other date format.
     *
     * @return int a Unix timestamp.
     */
    public function getExpirationTime();
    
    /**
     * Returns the content contained by this item.
     *
     * @return mixed the content of this item.
     */
    public function getCacheData();
    
    /**
     * Returns true if this item has outlived it's expiration time.
     *
     * @return bool true if this item should be deleted, false otherwise.
     */
    public function hasExpired();
}
