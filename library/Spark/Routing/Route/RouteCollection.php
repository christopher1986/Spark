<?php

namespace Spark\Routing\Route;

use ArrayIterator;
use Countable;
use IteratorAggregate;

use Spark\Collection\Set;

class RouteCollection implements IteratorAggregate, Countable
{
    /**
     * A collection containing routes.
     *
     * @var array
     */
    private $routes = array();
    
    /**
     * Construct a new RouteCollection.
     *
     * @param array|Traversable $routes (optional) a collection of routes.
     */
    public function __construct($routes = array())
    {
        $this->addAll($routes);
    }
    
    /**
     * Add the specified route to this collection.
     *
     * @param RouteInterface $route the route to add to this collection.
     * @return bool true if this collection did not already contain the specified route.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    public function add($name, RouteInterface $route)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
    
        $this->routes[$name] = $route;
        return true;
    }
    
    /**
     * Add to this collection all of the routes that are contained in the specified collection.
     *
     * @param RouteCollection $routes collection containing routes to add to this collection.
     * @return bool true if the collection has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @throws \InvalidArgumentException if the given collection contains keys which are not of type 'string'.
     * @see RouteCollection::add($name, $route);
     */
    public function addAll($routes)
    {
        if (!is_array($routes) && !($routes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($routes) ? get_class($routes) : gettype($routes))
            ));
        }
    
        foreach ($routes as $name => $route) {
            $this->add($name, $route);
        }
        
        return true;
    }
    
    /**
     * Returns true if this collection has a route for the given name.
     *
     * @param RouteInterface $route the route whose presence will be tested.
     * @return bool true if this collection contains the specified route, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function contains($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
	    
	    return (isset($this->routes[$name]));
    }
    
    /**
     * Returns true if this collection contains all routes contained in the specified collection.
     *
     * @param array|\Traversable $routes collection of route names whose presence will be tested.
     * @return bool true if this collection has a route for all the given route names, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @throws \InvalidArgumentException if the given collection contains elements which are not of type 'string'.
     * @see RouteCollection::contains($name)
     */
    public function containsAll($names)
    {
        if (!is_array($names) && !($names instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($names) ? get_class($names) : gettype($names))
            ));
        }
        
        $contains = true;
        foreach ($names as $name) {
            $contains = $this->contains($name);
            if (!$contains) {
                break;
            }
        }
        
        return $contains;
    }
    
    /**
     * Removes from this collection the route that matches the given route name.
     *
     * @param string $name the name of the route to remove.
     * @return mixed the route that was removed from the collection, or null if the route was not found.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function remove($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
    
        $removed = false;
        if (isset($this->routes[$name])) {
            unset($this->routes[$name]);
            $removed = true;
        }
        
        return $removed;
    }

    /**
     * Removes from this collection all of the routes that match the given route names.
     *
     * @param array|\Traversable $names collection containing the names of routes that will be removed from this collection.
     * @return bool true if the collection has changed, false otherwise.
     * @throws InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @throws InvalidArgumentException if the given collection contains elements which are not of type 'string'.
     * @see RouteCollection::remove($name)
     */
    public function removeAll($names)
    {
        if (!is_array($routes) && !($routes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($routes) ? get_class($routes) : gettype($routes))
            ));
        }
       
        $removed = false; 
        foreach ($names as $name) {
            if ($this->removed($name)) {
                $removed = true;
            }
        }
        
        return $removed;
    }
    
    /**
     * Removes all routes from this collection. The collection will be empty after this call returns.
     *
     * @return void
     */
    public function clear()
    {
        $this->routes = array();
    }
    
    /**
     * Returns true if this collection is considered to be empty.
     *
     * @return bool true is this collection contains no routes, false otherwise.
     */
    public function isEmpty()
    {
       return ($this->count() == 0);
    }
    
    /**
     * Returns the number of routes contained by this collection.
     *
     * @return int the number of routes contained by this collection.
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Returns an array containing all routes in this collection. The caller is free to modify the returned
     * array since it has no reference to the actual routes contained by this collection.
     *
     * @return array an array containing all routes from this collection.
     */
    public function toArray()
    {
        return $this->routes;
    }

    /**
     * Returns an iterator over the routes in this collection.
     *
     * @return Traversable an iterator over the routes in this collection.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }
    
    /**
     * Add the given prefix to path of all routes contained by this collection.
     *
     * @param string $prefix the prefix to add to a path.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function addPrefix($prefix)
    {
        if (!is_string($prefix)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($prefix) ? get_class($prefix) : gettype($prefix))
            ));
        }
        
        foreach ($this->routes as $route) {
            $route->setPath(trim($prefix, '/') . $route->getPath());
        }
    }
    
    /**
     * Set the host for all routes contained contained by this collection.
     *
     * @param string $host the host to set for all routes.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function setHost($host)
    {
        if (!is_string($prefix)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($prefix) ? get_class($prefix) : gettype($prefix))
            ));
        }
        
        foreach ($this->routes as $route) {
            $route->setHost($host);
        }   
    }
    
    /**
     * Set one or more request methods for all routes contained by this collection.
     *
     * @param string|array $methods the request methods to set for all routes.
     */
    public function setMethods($methods)
    {
        foreach ($this->routes as $route) {
            $route->setMethods($methods);
        } 
    }
    
    /**
     * Set one or more schemes for all routes contained by this collection.
     *
     * @param string|array $schemes the schemes to set for all routes.
     */
    public function setSchemes($schemes)
    {
        foreach ($this->routes as $route) {
            $route->setSchemes($schemes);
        } 
    }
    
    /**
     * Add one or more constraints for all routes contained by this collection.
     *
     * @param array|Traversable $constraints the constraints which will be added to all routes.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function addConstraints($constraints)
    {        
        if (!is_array($constraints) && !($constraints instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($constraints) ? get_class($constraints) : gettype($constraints))
            ));
        }
        
        // convert traversable object to array.
        if ($constraints instanceof \Traversable) {
            $constraints = iterator_to_array($constraints);
        }
        
        foreach ($this->routes as $route) {
            $route->setConstraints(array_merge($route->getConstraints(), $constraints));
        }
    }
    
    /**
     * Add one or more default value parameters for all routes contained by this collection.
     *
     * @param array|Traversable|string $defaults the default values which will be added to all routes.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function addDefaults($defaults)
    {        
        if (!is_array($defaults) && !($defaults instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($defaults) ? get_class($defaults) : gettype($defaults))
            ));
        }
        
        // convert traversable object to array.
        if ($defaults instanceof \Traversable) {
            $defaults = iterator_to_array($defaults);
        }
           
        foreach ($this->routes as $route) {
            $route->setDefaults(array_merge($route->getDefaults(), $defaults));
        }
    }
}
