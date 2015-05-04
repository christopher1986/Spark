<?php

namespace Spark\Routing\Matcher;

use Spark\Http\Request;
use Spark\Routing\Route\CompiledRoute;
use Spark\Routing\Route\RouteMatch;

class UrlMatcher implements UrlMatcherInterface
{
    /**
     * A collection of compiled routes.
     *
     * @var array
     */
    private $routes = array();

    /**
     * {@inheritDoc}
     */
    public function setRoutes($routes)
    {
        if (!is_array($routes) && !($routes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($routes) ? get_class($routes) : gettype($routes))
            ));
        }
    
        $this->routes = array_filter($routes, function($route) {
            return ($route instanceof CompiledRoute);
        });
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRoutes()
    {        
        return $this->routes;
    }
    
    /**
     * {@inheritDoc}
     */
    public function match(Request $request)
    {
        $routeMatch = null;
        
        $routes = $this->getRoutes();
        foreach ($routes as $routeName => $route) {
        
            // match request path with regular expression.
            if (!preg_match('#^' . $route->getRegex() . '$#', $request->getPathInfo(), $matches)) {
                continue;
            }

            // match if set the host.
            if ($route->getHost() !== '' && $request->getHost() !== $route->getHost()) {
                continue;
            }
            
            // match if set the HTTP scheme.
            $schemes = $route->getSchemes();
            if (!empty($schemes) && !in_array($request->getScheme(), $schemes)) {
                continue;
            }
            
            // match if set the request method.
            $methods = $route->getMethods();
            if (!empty($methods) && !in_array($request->getMethod(), $methods)) {
                continue;
            }

            // populate array with values.
            $params = array();
            foreach ($route->getParams() as $paramName) {
                if (isset($matches[$paramName])) {
                    $params[$paramName] = rawurldecode($matches[$paramName]);
                }
            }
            
            $routeMatch = new RouteMatch($routeName);
            $routeMatch->setParams(array_merge($route->getDefaults(), $params));
            
            break;  
        }

        return $routeMatch;
    }
}
