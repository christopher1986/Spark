<?php

namespace Spark\Routing;

use Spark\Routing\Matcher\UrlMatcherAwareInterface;
use Spark\Routing\Matcher\UrlMatcherInterface;
use Spark\Routing\Matcher\UrlMatcher;
use Spark\Routing\Route\RouteCollection;
use Spark\Routing\Route\CompiledRouteCollection;
use Spark\Routing\Route\RouteInterface;
use Spark\Routing\Route\CompiledRoute;
use Spark\Http\RequestInterface;

class Router implements RouterInterface, UrlMatcherAwareInterface
{
    /**
     * A collection of url matchers.
     *
     * @var UrlMatcherInterface
     */
    private $matcher;
    
    /**
     * A collection of routes.
     *
     * @var RouteCollection
     */
    private $routes;
    
    /**
     * A collection of compiled routes.
     *
     * @var array
     */
    private $compiled;
    
    /**
     * A route compiler.
     *
     * @var RouteCompiler
     */
    private $compiler;
        
    /**
     * A route parser.
     *
     * @var RouteParser
     */
    private $parser;
    
    /**
     * Construct a new RouteManager.
     */
    public function __construct()
    {
        $this->routes   = new RouteCollection();
        $this->compiler = new RouteCompiler();
        $this->parser   = new RouteParser();
    }
    
    /**
     * {@inheritDoc}
     */
    public function addRoutes($routes)
    {
        $modified = $this->routes->addAll($routes);
        if ($modified) {
            $this->clearCompiledRoutes();
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route) 
    {
        $modified = $this->routes->add($route);
        if ($modified) {
            $this->clearCompiledRoutes();
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clearRoutes()
    {
        $this->routes->clear();
        $this->clearCompiledRoutes();
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasRoute(RouteInterface $route)
    {
        return $this->routes->contains($route);
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeAllRoutes($routes)
    {
        $modified = $this->routes->removeAll($routes);
        if ($modified) {
            $this->clearCompiledRoutes();
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function retainAllRoutes($routes)
    {
        $modified = $this->routes->retainAll($routes);
        if ($modified) {
            $this->clearCompiledRoutes();
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove(RouteInterface $route)
    {
        $modified = ($this->routes->remove($route) !== null);
        if ($modified) {
            $this->clearCompiledRoutes();
        }
        
        return $modified;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRoutes()
    {
        return $this->routes->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function setUrlMatcher(UrlMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getUrlMatcher()
    {
        if ($this->matcher === null) {
            $this->matcher = new UrlMatcher();
        }
    
        return $this->matcher;
    }
    
    /**
     * {@inheritDoc}
     */
    public function match(RequestInterface $request)
    {
        if (empty($this->compiled) || count($this->compiled) != count($this->routes)) {
            $this->clearCompiledRoutes();
            $this->compiled = $this->compileRoutes($this->routes);
        }
        
        $params = array();
        if (($matcher = $this->getUrlMatcher()) !== null) {        
            $matcher->setRoutes($this->compiled);
            $params = $matcher->match($request);
        }
        
        return $params;
    }
    
    /**
     * Compiles the given collection routes.
     *
     * @param array|Traversable $route the routes to compiled.
     * @return array a collection of compiled routes.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    private function compileRoutes($routes)
    {
        if (!is_array($routes) && !($routes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($routes) ? get_class($routes) : gettype($routes))
            ));
        }
        
        $compiled = array();
        foreach ($routes as $name => $route) {
            // apply constraints to the vistor.
            $this->compiler->setConstraints($route->getConstraints());
            // parse path of route into an AST.
            $tree = $this->parser->parse($route->getPath());
            $tree->accept($this->compiler);
            
            // compile a new immutable route.
            $compiled[$name] = new CompiledRoute($route, $this->compiler->getRegex(), $this->compiler->getParams());
            
            // reset the visitor.
            $this->compiler->reset();
        }
        
        return $compiled;
    }
    
    /**
     * Removes all compiled routes.
     *
     * @return void
     */
    private function clearCompiledRoutes()
    {
        $this->compiled = array();
    }
}
