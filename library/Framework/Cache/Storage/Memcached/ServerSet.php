<?php

namespace Framework\Cache\Storage\Memcached;

use Framework\Collection\Set;
use Framework\Util\Arrays;
use Framework\Util\Strings;

/**
 * A ServerSet is a set that stores {@link Server} objects, and does not allow duplicate objects.
 *
 * @autor Chris Harris 
 * @version 1.0.0
 */
class ServerSet extends Set
{
    /**
     * {@inheritDoc}
     *
     * @param Server|array|Traversable|string $server the server to add to the collection.
     * @throws InvalidArgumentException if the given argument is not a Server object, or if the given
     *                                  argument could not be converted into one.
     * @see Set::add($element)
     */
    public function add($server)
    {
        if (!($server instanceof ServerInterface)) {
            $server = $this->toServer($server);
        }
        
        if ($server === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string, array, Traversable or Server object as argument; received "%s"',
                __METHOD__,
                (is_object($host) ? get_class($host) : gettype($host))
            ));
        }
        
        parent::add($server);
    }
    
    /**
     * Creates a Server object for the given value.
     *
     * @param array|Traversable|string $value the value for which a Server object will be created.
     * @return Server|null a Server object, or null if no Server object could be created.
     */
    private function toServer($value)
    {        
        if ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        }
        
        $server = null;
        if (is_array($value)) {
            if (Arrays::isAssoc($arr)) {
                $host   = (isset($arr['host']) && is_string($arr['host'])) ? $arr['host'] : '';
                $port   = (isset($arr['port']) && is_numeric($arr['port'])) ? $arr['port'] : 11211;
                $weight = (isset($arr['weight']) && is_numeric($arr['weight'])) ? $arr['weight'] : 0;
            } else {
                $host   = (isset($arr[0]) && is_string($arr[0])) ? $arr[0] : '';
                $port   = (isset($arr[1]) && is_numeric($arr[1])) ? $arr[1] : 11211;
                $weight = (isset($arr[2]) && is_numeric($arr[2])) ? $arr[2] : 0;
            }
            
            $server = new Server($host, $port, $weight);
        } else if (is_string($value) && Strings::endsWith($value, 'memcached.sock')) {
            $server = new Server($value, 0, 0);
        }

        return $server;
    }
}
