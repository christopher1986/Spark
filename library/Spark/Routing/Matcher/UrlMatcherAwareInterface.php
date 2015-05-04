<?php

namespace Spark\Routing\Matcher;

interface UrlMatcherAwareInterface
{
    /**
     * Set a url matcher for this route manager.
     *
     * @param UrlMatcherInterface $matcher the url matcher.
     */
    public function setUrlMatcher(UrlMatcherInterface $matcher);
    
    /**
     * Returns the url matcher.
     *
     * @return UrlMatcherInterface the url matcher.
     */
    public function getUrlMatcher();
}
