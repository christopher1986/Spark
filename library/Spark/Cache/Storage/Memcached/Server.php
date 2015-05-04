<?php

namespace Spark\Cache\Storage\Memcached;

use Spark\Common\Equatable;

/**
 * The Server class represents a single memcached server. You cannot directly commnunicate with a server through this class
 * because the management of servers is handled by the memcached extension. So a Server classs is more like a container that
 * holds all the properties which as a whole allow the memcached extension to communicate with a memcache server.
 *
 * @author Chris Harris 
 * @version 1.0.0
 * @link http://php.net/manual/en/memcached.addserver.php
 */
class Server implements ServerInterface, Equatable
{
    /**
     * The host to connect with.
     *
     * @var string
     */
    private $host;
    
    /**
     * The server port.
     * 
     * @var int
     */
    private $port;
    
    /**
     * The weight of the server within the server pool.
     *
     * @var int
     */
    private $weight;
    
    /**
     * Create server.
     *
     * @param string $host the hostname of the memcache server.
     * @param int $port the port on which memcache is running.
     * @param int $weight the weight of the server.
     */
    public function __construct($host = '', $port = 11211, $weight = 0)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setWeight($weight);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setHost($host)
    {
	    if (!is_string($host)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($host) ? get_class($host) : gettype($host))
            ));
	    }
	    
	    $this->host = $host;
    }
    
    /**
     * {@inheritDoc}
     *
     * The memcached extension will set the Memcached::RES_HOST_LOOKUP_FAILED result code for 
     * data-related operations if the provided hostname is invalid. As of version 2.0.0b1 the
     * hostname may also be specified as the path of a UNIX domain socket.
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setPort($port)
    {
        if (!is_numeric($port)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($port) ? get_class($port) : gettype($port))
            ));
        } else if ($port < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: port number cannot be a negative number; received "%d" instead',
                __METHOD__,
                $port)
            )); 
        }
        
        $this->port = (int) $port;
    }
    
    /**
     * {@inheritDoc}
     *
     * Usually the port is 11211. As of version 2.0.0b1 the port number should be set to 0 when
     * using UNIX domain sockets.
     */
    public function getPort()
    {
        return $this->port;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setWeight($weight)
    {
        if (!is_numeric($weight)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($weight) ? get_class($weight) : gettype($weight))
            ));
        } else if ($weight < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: weight of server must be larger than 0; received "%d" instead',
                __METHOD__,
                $weight)
            )); 
        }
        
        $this->weight = $weight;
    }
    
    /**
     * {@inheritDoc}
     *
     * The weight controls the probability of the server being selected for operations. It's only used 
     * with consistent distribution option and usually corresponds to the amount of memory available to 
     * memcache on that server.
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
     * {@inheritDoc}
     */
    public function equals($server)
    {
        if ($server instanceof self) {
            if ($server->getHost() !== $this->getHost()) {
                return false;
            }
            if ($server->getPort() !== $this->getPort()) {
                return false;
            }
            return true;
        }
        return false;
    }
}
