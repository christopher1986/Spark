<?php

namespace Spark\Routing\Route;

interface RouteInterface extends ImmutableRouteInterface
{
    /**
     * Set the pattern for a path.
     *
     * @param string $path the pattern for a path.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function setPath($path);
    
    /**
     * Set the host this route is restricted to. 
     *
     * A route which lacks a host will be match all hosts.
     *
     * @param string $host the host to which the route will be restricted.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function setHost($host);
    
    /**
     * Set the request methods this route will match. 
     *
     * A route which has no request methods will match all possible request methods. So for example to restrict
     * this route to GET and POST requests only the following array can be passed to this method:
     *
     * array(
     *     'GET',
     *     'POST',
     * );
     *
     * Although the example above uses uppercase request methods, both uppercase and lowercase request methods are 
     * treated uniformly by the route.
     *
     * @param array|string $methods one or more request methods to restrict this route to.
     */
    public function setMethods($methods);
    
    /**
     * Set the URL schemes this route will match.
     *
     * A route which has no scheme will match all possible schemes. So for example to restrict this route to
     * a Transport Layer Security (TLS) scheme the following array can be passed to this method:
     *
     * array(
     *     'https',
     * );
     *
     * Although the example above uses a scheme in lowercase, both uppercase and lowercase schemes are treated
     * uniformly by the route.
     *
     * @param array|string $schemes one or more schemes to restrict this route to.
     */
    public function setSchemes($schemes);
    
    /**
     * Set the constraints of one or more route parameters within the path pattern.
     *
     * A route which has no constraints will match all valid characters for a Uniform Resource Locator (URL).
     * A small example is given below to illustrate how one or more constraints could be applied to a route.
     *
     * $route = new Route('/products/sku/:sku/');
     * $route->setConstraints(array(
     *     'sku' => '[0-9]+',
     * ));
     *
     * This route will will only match a url whose path starts with "/products/sku/" and is followed by a 
     * numeric value, without the constraint the route parameter "sku" could be any possible character
     * allowed within a URL.
     *
     * @param array|Traversable $constraints one or more constraints to be assigned to the route parameters.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function setConstraints($constraints);
    
    /**
     * Set a collection of default values for this route.
     *
     * A default value, also known as as placeholder allows a route parameter to become optional. A small 
     * example is given below to illustrate how one or more default values could be applied to a route.
     *
     * $route = new Route('/products/sku/:sku/');
     * $route->setDefaults(array(
     *     'sku' => 148259,
     * ));
     *
     * This route will now match urls that lack a sku value for the "sku" parameter and will use the
     * default value "148259" instead. 
     *
     * @param array|Traversable $defaults one or more default value parameters.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function setDefaults($defaults);
}
