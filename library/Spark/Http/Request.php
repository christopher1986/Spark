<?php

namespace Spark\Http;

/**
 * The Request class provides useful information about the current HTTP request.
 *
 * Although most information found in the Request class is also retrievable 
 * through the use of PHP's built-in functionality, that information would 
 * still need to be normalized.
 * 
 * @author Chris Harris
 * @version 0.0.1
 */
class Request implements RequestInterface
{
    /**
     * A scheme for http request.
     *
     * @var string
     */
    const SCHEME_HTTP  = 'http';

    /**
     * A scheme for https request.
     *
     * @var string
     */
    const SCHEME_HTTPS = 'https';

    /**
     * The request URI.
     *
     * @var string
     */
    protected $requestUri;

    /**
     * The base url
     *
     * @var string
     */
    protected $baseUrl;
    
    /**
     * The path between the base url and querystring.
     *
     * @var string
     */
    protected $_pathInfo = '';

    /**
     * Create a new Request object which will attempt to populate itself using
     * the given URI.
     *
     * @param string $uri the URI from which this request object will be formed.
     */
    public function __construct($uri = null)
    {
        if (is_string($uri)) {
            // create request from the given URI.
            $this->setRequestUri($uri);
        } else {
            // create request from the current URI.
            $this->setRequestUri();
        }
    }
    
    /**
     * Set the request URI on which the instance operates.
     *
     * @param string $requestUri the request URI.
     * @return Request allows for method chaining.
     */
    public function setRequestUri($requestUri = null)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            
            // remove scheme and http host from URI.
            $schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
            
            // set $_GET value if available.
            if (($pos = strpos($requestUri, '?')) !== false) {
                // get key/value pairs to populate $_GET superglobal.
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $this->setQuery($vars);
            }
        }
           
        $this->requestUri = $requestUri;
        return $this;
    }

    /**
     * Returns the request URI on which the instance operates.
     *
     * @return string the request URI.
     */
    public function getRequestUri()
    {
        if (empty($this->requestUri)) {
            $this->setRequestUri();
        }

        return $this->requestUri;
    }
    
    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Do not use the full URI when providing the base. The following are
     * examples of what not to use:
     * - http://example.com/admin (should be just /admin)
     * - http://example.com/subdir/index.php (should be just /subdir/index.php)
     *
     * If no $baseUrl is provided, attempts to determine the base URL from the
     * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
     * ORIG_SCRIPT_NAME in its determination.
     *
     * @param mixed $baseUrl an optional base url set.
     * @return Request allows for method chaining.
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ((null !== $baseUrl) && !is_string($baseUrl)) {
            return $this;
        }

        if ($baseUrl === null) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
            } else {
                // Backtrack up the script_filename to find the portion matching
                // php_self
                $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
                $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
                $segs    = explode('/', trim($file, '/'));
                $segs    = array_reverse($segs);
                $index   = 0;
                $last    = count($segs);
                $baseUrl = '';
                do {
                    $seg     = $segs[$index];
                    $baseUrl = '/' . $seg . $baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
            }

            // Does the baseUrl have anything in common with the request_uri?
            $requestUri = $this->getRequestUri();

            if (0 === strpos($requestUri, $baseUrl)) {
                // full $baseUrl matches
                $this->baseUrl = $baseUrl;
                return $this;
            }

            if (0 === strpos($requestUri, dirname($baseUrl))) {
                // directory portion of $baseUrl matches
                $this->baseUrl = rtrim(dirname($baseUrl), '/');
                return $this;
            }

            $truncatedRequestUri = $requestUri;
            if (($pos = strpos($requestUri, '?')) !== false) {
                $truncatedRequestUri = substr($requestUri, 0, $pos);
            }

            $basename = basename($baseUrl);
            if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                // no match whatsoever; set it blank
                $this->baseUrl = '';
                return $this;
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            if ((strlen($requestUri) >= strlen($baseUrl))
                && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
            {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }
    
    /**
     * Returns the base url from this request.
     *
     * @param bool $raw if true the original base url is returned,
     *                  otherwise the base url is url decoded.
     * @return string the base url.
     */
    public function getBaseUrl($raw = false)
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl();
        }

        return ($raw == false) ? urldecode($this->baseUrl) : $this->baseUrl;
    }
    
    /**
     * Set the path info string
     *
     * @param string|null $pathInfo the path info to set.
     * @return Request allows for method chaining.
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $baseUrl = $this->getBaseUrl();
            $baseUrlRaw = $this->getBaseUrl(false);
            $baseUrlEncoded = urlencode($baseUrlRaw);
        
            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }
        
            // Remove the query string from REQUEST_URI
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }
            
            if (!empty($baseUrl) || !empty($baseUrlRaw)) {
                if (strpos($requestUri, $baseUrl) === 0) {
                    $pathInfo = substr($requestUri, strlen($baseUrl));
                } elseif (strpos($requestUri, $baseUrlRaw) === 0) {
                    $pathInfo = substr($requestUri, strlen($baseUrlRaw));
                } elseif (strpos($requestUri, $baseUrlEncoded) === 0) {
                    $pathInfo = substr($requestUri, strlen($baseUrlEncoded));
                } else {
                    $pathInfo = $requestUri;
                }
            } else {
                $pathInfo = $requestUri;
            }
        
        }

        $this->pathInfo = (string) $pathInfo;
        return $this;
    }
    
    /**
     * Returns everything between the base url and querystring.
     *
     * @return string the path info.
     */
    public function getPathInfo()
    {
        if (empty($this->pathInfo)) {
            $this->setPathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * Set $_GET value.
     *
     * @param string|array|\Traversable the specificaton to set.
     * @param mixed|null $value the value for the specificaton.
     * @return Request allows for method chaining.
     */
    public function setQuery($spec, $value = null)
    {
        if (is_null($value) && (!is_array($spec) && !($spec instanceof \Traversable))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value passed to %s; must be either array of values or key/value pair',
                __METHOD__
            ));
        }
        
        if ((null === $value) && (is_array($spec) || ($spec instanceof \Traversable))) {
            // iterate through array and set query variables.
            foreach ($spec as $key => $value) {
                $this->setQuery($key, $value);
            }
            
            return $this;
        }
        
        // set $_GET value.
        $key = (string) $spec;
        $_GET[$key] = $value;
       
        return $this;
    }

    /**
     * Returns a member from the $_GET superglobal.
     *
     * @param string $key (optional) the name of the member.
     * @param mixed $default (optional) the default value if the key does not exist.
     * @return mixed the value associated with the key.
     */
    public function getQuery($key = null, $default = null)
    {
        if (is_null($key)) {
            return $_GET;
        }
    
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    /**
     * Set $_POST value.
     *
     * @param string|array|\Traversable the specificaton to set.
     * @param mixed|null $value the value for the specificaton.
     * @return Request allows for method chaining.
     */
    public function setPost($spec, $value = null) 
    {
        if (is_null($value) && (!is_array($spec) && !($spec instanceof \Traversable))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value passed to %s; must be either array of values or key/value pair',
                __METHOD__
            ));
        }
        
        if ((null === $value) && (is_array($spec) || ($spec instanceof \Traversable))) {
            // iterate through array and set post values.
            foreach ($spec as $key => $value) {
                $this->setPost($key, $value);
            }
            
            return $this;
        }
        
        // set $_POST value.
        $key = (string) $key;
        $_POST[$key] = $value;
       
        return $this;
    }

    /**
     * Returns a member of the $_POST superglobal.
     *
     * @param string $key (optional) the name of the member.
     * @param mixed $default (optional) the default value if the key does not exist.
     * @return mixed the value associated with the key.
     */
    public function getPost($key = null, $default = null)
    {
        if (is_null($key)) {
            return $_POST;
        }
    
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }
    
    /**
     * Returns a member of the $_COOKIE superglobal.
     *
     * @param string $key (optional) the name of the member.
     * @param mixed $default (optional) the default value if the key does not exist.
     * @return mixed the value associated with the key.
     */
    public function getCookie($key = null, $defaults = null)
    {
        if (is_null($key)) {
            return $_COOKIE;
        }
    
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    /**
     * Returns a member of the $_SERVER superglobal.
     *
     * @param string $key (optional) the name of the member.
     * @param mixed $default (optional) the default value if the key does not exist.
     * @return mixed the value associated with the key.
     */
    public function getServer($key = null, $default = null) 
    {
        if (is_null($key)) {
            return $_SERVER;
        }
    
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }
    
    /**
     * Return the method by which the request was made.
     *
     * @return string the method for this request.
     */ 
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }
    
    /**
     * Returns true if a POST request was made, false otherwise.
     *
     * @return bool returns true if this is a POST request, false otherwise.
     */
    public function isPost()
    {
        return ('POST' == $this->getMethod());
    }
    
    /**
     * Returns true if a GET request was made, false otherwise.
     *
     * @return bool returns true if this is a GET request, false otherwise.
     */
    public function isGet()
    {
        return ('GET' == $this->getMethod());
    }

    /**
     * Returns true if a PUT request was made, false otherwise.
     *
     * @return bool returns true if this is a PUT request, false otherwise.
     */
    public function isPut()
    {
        return ('PUT' == $this->getMethod());
    }

    /**
     * Returns true if a DELETE request was made, false otherwise.
     *
     * @return bool returns true if this is a DELETE request, false otherwise.
     */
    public function isDelete()
    {
        return ('DELETE' == $this->getMethod());
    }
    
    /**
     * Returns true if this is a secure request, false otherwise.
     *
     * @return bool returns true if this a secure request,
     *              false otherwise.
     */
    public function isSecure()
    {
        return ($this->getScheme() === self::SCHEME_HTTPS);
    }

    /**
     * Returns true if an AJAX request was made, false otherwise.
     *
     * @return bool returns true if this is an AJAX request, false otherwise.
     */
    public function isXmlHttpRequest()
    {
        $httpx = $this->getServer('HTTP_X_REQUESTED_WITH');
        return (!is_null($httpx) && 'xmlhttprequest' == strtolower($httpx));
    }
    
    /**
     * Returns true if the request made by HEAD, false otherwise.
     *
     * @return bool returns true if this request was made by HEAD, false otherwise.
     */
    public function isHead()
    {
        return ('HEAD' == $this->getMethod());
    }
    
    /**
     * Returns the request URI scheme
     *
     * @return string the request URI scheme.
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }
    
    /**
     * Returns the HTTP host.
     *
     * @return string the HTTP host.
     */
    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name = $this->getServer('SERVER_NAME');
        $port = $this->getServer('SERVER_PORT');

        if(null === $name) {
            return '';
        } elseif (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }
    
    /**
     * Returns the domain part from this request which may also include the port if the server runs on a port other than 80.
     *
     * @return string return the domain part from this request.
     */
    public function getDomain()
    {
        $domain = $this->getHttpHost();
        // remove 'www' from host since it's only used for convention.
        if (is_string($host) && strpos('www.', $host) !== false) {
            $domain = str_replace('www.', '', $host);
        }
        
        return $domain;
    }
    
    /**
     * Returns the URL the client used to make the request.
     *
     * @return string the request url made by the client.
     */
    public function getRequestUrl()
    {
        return $this->getScheme() . '://' . $this->getHttpHost() . $this->getRequestUri();
    }
}
