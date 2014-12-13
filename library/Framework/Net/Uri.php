<?php

namespace Framework\Net;

class Uri implements UriInterface
{
    /**
     * characters that are allowed in a URI but do not have a reserved purpose.
     *
     * @var string
     */
    const CHARS_UNRESERVED = 'a-zA-Z0-9\-_\.!~\*\'\(\)'; 

    /**
     * special characters whose usage is limited due to their reserved purpose.
     *
     * @var string
     */
    const CHARS_RESERVED = ';/?:@=\+\$,';

    /**
     * a scanner to convert a uri string to a list of tokens.
     *
     * @var UriScanner
     */
    private $scanner;

    /**
     * the scheme name.
     *
     * @var string
     */
    private $scheme;
    
    /**
     * the user-information.
     *
     * @var string
     */
    private $userInfo;
    
    /**
     * the host name.
     *
     * @var string
     */
    private $host;
    
    /**
     * the port number.
     *
     * @var int
     */
    private $port;
    
    /**
     * the path part.
     *
     * @var string
     */
    private $path;
    
    /**
     * the query part.
     *
     * @var string
     */
    private $query;
    
    /**
     * the fragment part.
     *
     * @var string
     */
    private $fragment;

    /**
     * Create a new uri from the given string.
     *
     * @param string|null the string to be parsed into a uri.
     */
    public function __construct($uri = null)
    {
        if (is_string($uri)) {
            $this->create($uri);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create($uri)
    {
        if (!is_string($uri)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }
    
        $scanner = new UriScanner($uri);
        $tokens = $scanner->scan();
    }
    
    /**
     * Set the scheme for this URI.
     *
     * @param string $scheme the scheme part.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setScheme($scheme)
    {        
        $this->scheme = $this->normalizeScheme($scheme);
    }   
      
    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }
    
    /**
     * Set the user authority part (better known as user-information) this URI.
     *
     * @param string $userInfo the authority part.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setUserInfo($userInfo)
    {
        $this->userInfo = $this->normalizeUserInfo($userInfo);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }
    
    /**
     * Set the host for this URI.
     *
     * @param string $host the host part.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setHost($host)
    {
        $this->host = $this->normalizeHost($host);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * Set the port number for this URI.
     *
     * @param int $port the port part.
     * @throws \InvalidArgumentException if the given argument is not of type 'int'.
     */
    protected function setPort($port)
    {
        $this->port = $this->normalizePort($port);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the path for this URI.
     *
     * @param string $path the path.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setPath($path) 
    {
        $this->path = $this->normalizePath($path);    
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->query;
    }
    
    /**
     * Set the query for this URI.
     *
     * @param string $query the query part.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setQuery($query)
    {
        $this->query = $this->normalizeQuery($query);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * Set the framgent for this URI.
     *
     * @param string $scheme the fragment part.
     * @throws \InvalidArgumentException if the given argument is not of type 'string'.
     */
    protected function setFragment($fragment)
    {
        $this->fragment = $this->normalizeFragment($fragment);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isAbsolute()
    {
        return ($this->getScheme() !== null);
    }
    
    /**
     * {@inheritDoc}
     */
    public function normalize()
    {}
    
    /**
     * {@inheritDoc}
     */
    public function relative(UriInterface $uri)
    {}
    
    /**
     * {@inheritDoc}
     */
    public function resolve($uri)
    {}
    
    /**
     * {@inheritDoc}
     */
    public function toString()
    {}
    
    /**
     * 
     *
     *
     */
    private function normalizePath($path)
    {}
    
    private function normalizeUserInfo($path)
    {}

    private function normalizeHost($path)
    {}
    
    private function normalizePort($path)
    {}
    
    private function normalizeQuery($query)
    {}
    
    private function normalizeFragment($fragment)
    {}
    
    
    private function decode($str)
    {
        
    }
}
