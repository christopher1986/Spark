<?php

namespace Spark\Http;

/**
 * The RequestInterface describes the minimum implementation needed for a 
 * request to work, most classes however that implement this interface will 
 * add additional methods.
 *
 * @author Chris Harris
 * @version 1.0.0 
 */
interface RequestInterface
{        
    /**
     * Set the request URI on which the instance operates.
     *
     * @param string $requestUri the request URI.
     * @return RequestInterface allows for method chaining.
     */
    public function setRequestUri($uri = null);
    
    /**
     * Returns the request URI on which the instance operates.
     *
     * @return string the request URI.
     */
    public function getRequestUri();
    
    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * @param mixed $baseUrl an optional base url set.
     * @return RequestInterface allows for method chaining.
     */
    public function setBaseUrl($baseUrl = null);
    
    /**
     * Returns the base url from this request.
     *
     * @param bool $raw if true the original base url is returned,
     *                  otherwise the base url is url decoded.
     * @return string the base url.
     */
    public function getBaseUrl($raw = false);
    
    /**
     * Set the path info string
     *
     * @param string|null $pathInfo the path info to set.
     * @return RequestInterface allows for method chaining.
     */
    public function setPathInfo($pathInfo = null);
    
    /**
     * Returns everything between the base url and querystring.
     *
     * @return string the path info.
     */
    public function getPathInfo();
}
