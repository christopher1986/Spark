<?php

namespace Spark\Routing\Route;

interface CompiledRouteInterface extends ImmutableRouteInterface
{
    /**
     * Returns the regular expression to match.
     *
     * @return string a regular expression.
     */  
    public function getRegex();
    
    /**
     * Returns the parameters whose values need to be obtained from a url match. 
     * 
     * @return array a collection of parameter names.
     */
    public function getParams();
}
