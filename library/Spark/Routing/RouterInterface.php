<?php

namespace Spark\Routing;

use Spark\Http\RequestInterface;
use Spark\Routing\Route\RouteInterface;

interface RouterInterface
{
    /**
     * Append the given routes to the router.
     *
     * @param array|Traversable $routes the routes to append.
     * @return bool true if the routes were added, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function addRoutes($routes);
    
    /**
     * Append the given route to the router.
     *
     * @param RouteInterface $route the rout to append.
     * @return bool true if the given route was added, false otherwise.
     */
    public function addRoute(RouteInterface $route);
    
    /**
     * Removes all routes from the router.
     *
     * @return void
     */
    public function clearRoutes();
    
    /**
     * Tests whether the given route exists
     *
     * @param RouteInterface $route the route whose presence will be tested.
     * @return bool true if the given route exists, false otherwise.
     */
    public function hasRoute(RouteInterface $route);
    
    /**
     * Tests whether the given request matches a route.
     *
     * @param Request $request the request to match.
     */
    public function match(RequestInterface $request);
}
