<?php

namespace Spark\Cache\Storage\Memcached;

/**
 * The ServerInterface defines the methods that allow an object to store information about a single 
 * memcache server. A class that implements this interface is also known as a container, or sometimes
 * referred to as a Plain Old PHP Object (POPO).
 * 
 * @author Chris Harris
 * @version 1.0.0
 */
interface ServerInterface
{
    /**
     * Set the hostname of the memcache server.
     *
     * @param string $host the hostname of the memcache server.
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @see Server::getHost()
     */
    public function setHost($host);
    
    /**
     * Returns the hostname of the memcache server.
     *
     * The memcached extension will set the Memcached::RES_HOST_LOOKUP_FAILED result code for 
     * data-related operations if the provided hostname is invalid. As of version 2.0.0b1 the
     * hostname may also be specified as the path of a UNIX domain socket.
     *
     * @return string the hostname of the memcache server.
     */
    public function getHost();
    
    /**
     * Set the port on which memcache is running.
     *
     * @param int $port the port on which memcache is running.
     * @throws InvalidArgumentException if the given argument if not numeric.
     * @throws InvalidArgumentException if the given port is a negative number.
     * @see Server::getPort()
     */
    public function setPort($port);
    
    /**
     * Returns the port on which memcache is running.
     *
     * Usually the port is 11211. As of version 2.0.0b1 the port number should be set to 0 when
     * using UNIX domain sockets.
     *
     * @return int the port on which memcache is running.
     */
    public function getPort();
    
    /**
     * Set the weight of the server relative to the total weight of all the servers in the pool.
     *
     * @param int $weight the weight of the server.
     * @throws InvalidArgumentException if the given argument if not numeric.
     * @throws InvalidArgumentException if the given weight is a negative number.
     * @see Server::getWeight()
     */
    public function setWeight($weight);
    
    /**
     * Returns the weight of the server relative to the total weight of all the servers in the pool.
     *
     * @return int the weight of the server.
     */
    public function getWeight();
}
