<?php

namespace Spark\Routing\Matcher;

use Spark\Http\Request;
use Spark\Routing\Route\CompiledRouteCollection;

interface UrlMatcherInterface
{
    /**
     * Set a collection (compiled) routes. 
     *
     * Passing an empty {@link CompiledRouteCollection} array as argument is equivalent 
     * to removing all routes.
     *
     * @param Array|Traversable $routes a collection of compiled routes. 
     */
    public function setRoutes($routes);
    
    /**
     * Returns a collection of routes.
     *
     * @return array a collection of routes.
     * @see UrlMatcherInterface::setRoutes($routes)
     */
    public function getRoutes();
    
    /**
     * Test whether the given request matches a route.
     *
     * @param Request $request the request to match.
     * @return 
     */
    public function match(Request $request);
}
