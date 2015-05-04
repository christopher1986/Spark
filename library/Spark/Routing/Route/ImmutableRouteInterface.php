<?php 

namespace Spark\Routing\Route;

use Serializable;

interface ImmutableRouteInterface extends Serializable
{
    /**
     * Returns the pattern for a path.
     *
     * @return string the pattern for a path.
     */
    public function getPath();
        
    /** 
     * Returns if present the host to which this route is restricted.
     *
     * @return string|null the host to which the route is restricted, or null if the route applies to all hosts.
     * @see RouteInterface::setHost($host);
     */
    public function getHost();
    
    /**
     * Returns if present the request methods this route will match.
     *
     * @return array the request methods this route will match, or an empty array.
     * @see RouteInterface::setMethods($methods);
     */
    public function getMethods();
    
    /**
     * Returns if present the scheme this route will match.
     *
     * @return array the schemes this route will match.
     * @see RouteInterface::setSchemes($schemes);
     */
    public function getSchemes();
    
    /**
     * Returns if present the constraints for one or more route parameters.
     *
     *
     * @return array a collection of constraints, or an empty array this route has no constraints.
     * @see RouteInterface::setConstraints($constraints);
     */
    public function getConstraints();
    
    /**
     * Returns if present a collection of default values.
     *
     * @return array a collection of default value parameters, or an emtpy array if this route has no default values.
     */
    public function getDefaults();
}
