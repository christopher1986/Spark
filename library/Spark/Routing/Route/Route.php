<?php

namespace Spark\Routing\Route;

use Spark\Common\Equatable;
use Spark\Util\Strings;

class Route implements RouteInterface, Equatable
{
    /**
     * A path pattern to match.
     *
     * @var string
     */
    private $path = '';
    
    /**
     * A possible host to match.
     *
     * @var string
     */
    private $host = '';

    /**
     * A collection of request methods to match.
     *
     * @var array
     */
    private $methods = array();
    
    /**
     * A collection of URI schemes to match.
     *
     * @var array
     */
    private $schemes = array();
    
    /**
     * A collection of constraints for one or more route parameters.
     *
     * @array
     */
    private $constraints = array();

    /**
     * A collection of placeholders for one or more route parameters.
     *
     * @array
     */    
    private $defaults = array();

    /**
     * Construct a new Route.
     *
     * @param string $path the path pattern to match.
     * @param array $default a collection of default parameter values.
     * @param array $constraints a collection of constraints to be apply to one or more parameters.
     * @param string $host the host to restrict this route to.
     * @param array $schemes a collection of schemes to restrict this route to.
     * @param array $methods a collection of request methods to restrict this route to.
     */
    public function __construct($path, $defaults = array(), $constraints = array(), $host = '', $schemes = array(), $methods = array())
    {
        $this->setPath($path);
        $this->setDefaults($defaults);
        $this->setConstraints($constraints);
        $this->setHost($host);
        $this->setSchemes($schemes);
        $this->setMethods($methods);
    }

    /**
     * {@inheritDoc}
     */
    public function setPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($path) ? get_class($path) : gettype($path))
            ));
        }
        
        $this->path = Strings::addLeading(rtrim($path, '/'), '/');
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->path;
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
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setMethods($methods)
    {
        $this->methods = array_map('strtoupper', (array) $methods);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setSchemes($schemes)
    {
        $this->methods = array_map('strtolower', (array) $schemes);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSchemes()
    {
        return $this->schemes;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setConstraints($constraints)
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
        
        $constraints = array_map(array($this, 'sanitizeConstraint'), $constraints);
        $this->constraints = array_filter($constraints);
    }
    
    /**
     * Set a constraint fo a route parameters within the path pattern.
     *
     * @param string $name the name of the route parameter.
     * @param string $regex the constraint to put on the route parameter.
     * @see RouteInterface::setConstraints($constraints)
     */
    public function setConstraint($name, $regex)
    {    
        $this->constraints[$name] = $this->sanitizeConstraint($regex);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConstraints()
    {
        return $this->constraints;
    }
    
    /**
     * Tests whether a constraint is placed on the given route parameter.
     *
     * @param string $name the name of the route parameter.
     * @return bool true if a constraint exists for the given route parameter, false otherwise.
     */
    public function hasConstraint($name)
    {
        return (array_key_exists($name, $this->constraints));
    }
    
    /**
     * Clear all constraints from this route. After this call returns all constraints will be removed.
     *
     * @return void
     */
    public function clearConstraints()
    {
        $this->constraints = array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDefaults($defaults)
    {        
        if (!is_array($defaults) && !($defaults instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable object as argument; received "%s"',
                __METHOD__,
                (is_object($defaults) ? get_class($defaults) : gettype($defaults))
            ));
        }
     
        if ($defaults instanceof \Traversable) {
            $defaults = iterator_to_array($defaults);
        }
        
        $this->defaults = $defaults; 
    }
 
    /**
     * Set a default value for a route parameter.
     *
     * @param string $name the name of route parameter.
     * @param mixed $value the default value to be assigned to the route parameter.
     */
    public function setDefault($name, $value)
    {
        $this->defaults[$name] = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDefaults()
    {
        return $this->defaults;
    }
    
    /**
     * Tests whether a default value exists for the given route parameter.
     *
     * @param string $name the name of the route parameter.
     * @return bool true if a default value exists for the given route parameter, false otherwise.
     */
    public function hasDefault($name)
    {
        return (array_key_exists($name, $this->defaults));
    }
    
    /**
     * Clear all default values from this route. After this call returns all default values will be removed.
     *
     * @return void
     */
    public function clearDefaults()
    {
        $this->defaults = array();
    }
    
    /**
     * Sanitizes a constraint by removing any leading carets and trailing dollar signs from the 
     * given regular expression.
     *
     * Within a route the words "constraint" and "regular expression" can be used interchangeably. 
     * A route places constraints on a path using a regular expression, so within the context of 
     * a route these words both mean the same.
     *
     * @param string $regex the regular expression to sanitize.
     * @return string a sanitized constraint, or empty string on failure.
     */
    private function sanitizeConstraint($regex)
    {
        $regex = (is_string($regex)) ? $regex : '';        
        return rtrim(ltrim($regex, '^'), '$');
    }
    
    /**
     * {@inheritDoc}
     */
    public function equals($route)
    {
        if ($route instanceof self) {
            if ($route->getPath() !== $this->getPath()) {
                return false;
            }
            if ($route->getHost() !== $this->getHost()) {
                return false;
            }
            if (array_diff($route->getMethods(), $this->getMethods())) {
                return false;
            }
            if (array_diff($route->getSchemes(), $this->getSchemes())) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Returns a string representation of the compiled route.
     * 
     * @link http://php.net/manual/en/serializable.serialize.php
     */
    public function serialize()
    {
        return serialize(array(
            'path'        => $this->getPath(),
            'host'        => $this->getHost(),
            'methods'     => $this->getMethods(),
            'schemes'     => $this->getSchemes(),
            'constraints' => $this->getConstraints(),
            'defaults'    => $this->getDefaults(),
        ));
    }
    
    /**
     * Recreates the compiled route from a string that represents a route.
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->path        = $data['path'];
        $this->host        = $data['host'];
        $this->methods     = $data['methods'];
        $this->schemes     = $data['schemes'];
        $this->constraints = $data['constraints'];
        $this->defaults    = $data['defaults'];
    }
}
