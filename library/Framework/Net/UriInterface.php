<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Framework\Net;

/**
 * Represents a Uniform Resource Identifier (URI) reference.
 *
 * A URI object is consistent with the {@link https://www.ietf.org/rfc/rfc2396.txt Uniform Resource Identifiers (URI): Generic Syntax}. A URI instance
 * provides the ability to parse a string into a new URI and provides methods for normalizing, resolving and relativizing URI instances.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
interface UriInterface
{
    /**
     * Creates a URI by parsing the given string.
     *
     * @return UriInterface a new URI.
     * @throws MalformedUriException if the given string violates RFC 2396.
     * @link https://www.ietf.org/rfc/rfc2396.txt
     */
    public function create($str);
    
    /**
     * Returns the scheme name of this URI.
     *
     * @return string|null the scheme name of this URI, or null if the scheme name is undefined.
     */
    public function getScheme();
    
    /**
     * Returns the user-information of this URI.
     *
     * @return string the user-information of this URI, or null if the user-information is undefined.
     */
    public function getUserInfo();
    
    /**
     * Returns the host name of this URI.
     *
     * @return string|null the host name of this URI, or null if the host name is undefined.
     */
    public function getHost();
    
    /**
     * Returns the port number of this URI.
     *
     * @return int the port number of this URI, or -1 if the port is undefined.
     */
    public function getPort();
    
    /**
     * Returns the path of this URI.
     *
     * @return string|null the path of this URI, or null if if the path is undefined.
     */
    public function getPath();
    
    /**
     * Retuns the query of this URI.
     *
     * @return string|null the query of this URI, or null if the query is undefined.
     */
    public function getQuery();
    
    /**
     * Returns the fragment of this URI.
     *
     * @return string|null the fragment of this URI, or null if the fragment is undefined.
     */
    public function getFragment();
    
    /**
     * Tests whether this URI is absolute.
     *
     * @return bool true, if and only if this URI is absolute.
     */
    public function isAbsolute();
    
    /**
     * Normalize the path of this URI.
     *
     * The normalization is consistent with {@link https://www.ietf.org/rfc/rfc2396.txt RFC 2396}, section 5.2, step 6, sub-steps c 
     * through f; that is:
     *
     * 1. All "." segments are removed.
     * 2. If a ".." segment is preceded by a non-".." segment then both these segments are removed. This process is repreated until 
     *    it is no longer applicable.
     * 
     * A normalized path will begin with ".." segments if there were insufficient non-".." segments preceding them to allow their removal.
     * 
     * @return UriInterface a URI that is similar to this URI, but whose path has been normalized.
     */
    public function normalize();
    
    /**
     * Reconstructs the given URI in such a way that it's considered to be an relative URI of this URI.
     *
     * 1. If the scheme of the given URI is not equal to this URI, or if the path of this URI is not a prefix of the path of the given URI 
     *    then given URI is returned unchanged.
     * 2. Otherwise a new relative URI is constructed with query and fragment parts taken from the given URI and with a path that is
     *    computed by removing this URI's path from the beginning of the given URI's path.
     * 
     * @param UriInterface $uri the URI to reconstruct in such a way that it's considered to be relative.
     * @return UriInterface the resulting URI.
     * @throws \InvalidArgumentException if the given uri is null.
     */
    public function relative(UriInterface $uri);
    
    /**
     * Resolve the given string or URI against this URI.
     *
     * 1. If the given URI is already absolute then the given URI is returned unchanged.
     * 2. Otherwise a new URI is constructed with this URI's scheme and the given URI's query and fragment parts.
     * 3. If the given URI has an authority part (a.k.a. user-information) then the new URI authority part and path are taken from the given URI.
     * 4. Otherwise the new URI authority part and path taken from this URI, the path is computed as follows:
     *    a) If the given URI's path if absolute then the new URI's path is taken from the given URI.
     *    b) Otherwis the path of the given URI must be relative and the new URI's path is computed by resolving the path of the given URI with
     *       path of this URI. 
     *
     * @param UriInterface|string $uri a string that represents an URI or URI to resolve.
     * @return the resulting URI.
     * @throws MalformedUriException if the given string violates RFC 2396.
     */
    public function resolve($uri);
    
    /**
     * Returns a string representation of this URI.
     *
     * @return string the string representation of this URI.
     */
    public function toString();
}
