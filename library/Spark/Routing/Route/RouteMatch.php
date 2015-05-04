<?php

namespace Spark\Routing\Route;

/**
 * The RouteMatch contains information that was gathered when a route successfully matched a url.
 *  
 * @author Chris Harris
 * @version 1.0.0
 */
class RouteMatch
{
    /**
     * The name of the route that matched.
     *
     * @var string
     */
    private $routeName = '';
    
    /**
     * A collection of parameters collected from the route.
     *
     * @var array
     */
    private $params = array();
    
    /**
     * Construct a new RouteMatch.
     *
     * @param string $routeName the name of the route that matched.
     * @param array|Traversable $params (optional) the parameters associated with the matched route.
     */
    public function __construct($routeName, $params = array())
    {
        $this->setRouteName($routeName);
        $this->setParams($params);
    }
    
    /**
     * Set the name of the matched route.
     *
     * @param string $routeName the name of the route.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function setRouteName($routeName)
    {
	    if (!is_string($routeName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($routeName) ? get_class($routeName) : gettype($routeName))
            ));
	    }
	    
	    $this->routeName = $routeName;
    }
    
    /**
     * Returns the name of the matched route.
     *
     * @return string the name of the route.
     */
    public function getRouteName()
    {
        return $this->routeName;
    }
    
    /**
     * Set a collection consisting of key-value pairs for each parameter.
     *
     * @param array|Traversable $params a collection of parameters.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function setParams($params)
    {
        if (!is_array($params) && !($params instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($params) ? get_class($params) : gettype($params))
            ));
        }
        
        // convert traversable object to array.
        if ($params instanceof \Traversable) {
            $params = iterator_to_array($params);
        }
    
        $this->params = $params;
    }
    
    /**
     * Returns a collection of parameters. The collection returned consists of key-value pairs
     * where each key represents the name of a parameter.
     *
     * @return array a collection of parameters.
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /** 
     * Returns if present the value associated with the given parameter, otherwise the
     * default value is returned.
     *
     * @param string $name the name of the parameter whose value to return.
     * @param mixed $default the default value to return if the given parameter does not exist.
     * @return mixed the value for the given parameter, or the default value on failure.
     */
    public function getParam($name, $default = null)
    {   
        $value = $default;
        if (array_key_exists($name, $this->params)) {
            $value = $this->params[$name];
        }
        
        return $value;
    }
}
