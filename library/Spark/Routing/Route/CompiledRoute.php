<?php

namespace Spark\Routing\Route;

class CompiledRoute implements CompiledRouteInterface
{
    /**
     * The route that is decorated.
     *
     * @var RouteInterface
     */
    private $route;
    
    /**
     * A regular expression to match.
     *
     * @var string
     */
    private $regex = '';

    /**
     * A collection of route parameters.
     *
     * @var array
     */
    private $params = array();
    
    /**
     * Construct a new CompiledRoute.
     *
     * @param RouteInterface $route the route to decorate.
     * @param string $regex the regular expression to match.
     * @param array $params a collection of parameter names.
     */
    public function __construct(RouteInterface $route, $regex = '', $params = array())
    {
        $this->route = $route;
        $this->setRegex($regex);
        $this->setParams($params);
    }
    
    /**
     * Set the regular expression to match. The caret and dollar symbols will be stripped 
     * from the given regular expression.
     *
     * @param string $regex the regular expression.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function setRegex($regex)
    {
        if (!is_string($regex)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($regex) ? get_class($regex) : gettype($regex))
            ));
        }
        
        $this->regex = rtrim(ltrim($regex, '^'), '$');
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Set the parameters whose values will be obtained from a successful url match. 
     * 
     * @param array|Traversable $params a collection of parameter names.
     */
    private function setParams($params)
    {
        if (!is_array($params) || $params instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable object as argument; received "%s"',
                __METHOD__,
                (is_object($params) ? get_class($params) : gettype($params))
            ));
        }
        
        if ($params instanceof \Traversable) {
            $params = iterator_to_array($params);
        }
        
        $this->params = $params;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->route->getPath();
    }
        
    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->route->getHost();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMethods()
    {
        return $this->route->getMethods();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSchemes()
    {
        return $this->route->getSchemes();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConstraints()
    {
        return $this->route->getConstraints();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDefaults()
    {
        return $this->route->getDefaults();
    }

    /**
     * Returns a string representation of the compiled route.
     * 
     * @link http://php.net/manual/en/serializable.serialize.php
     */
    public function serialize()
    {
        return serialize(array(
            'route'  => $this->route,
            'regex'  => $this->getRegex(),
            'params' => $this->getParams(),
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

        $this->route  = $data['route'];
        $this->regex  = $data['regex'];
        $this->params = $data['params'];
    }
}
