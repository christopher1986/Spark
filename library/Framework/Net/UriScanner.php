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

use Framework\Io\Reader;
use Framework\Io\StringReader;
use Framework\Scanner\AbstractScanner;
use Framework\Scanner\TokenInterface;
use Framework\Scanner\Token;

/**
 * The UriScanner is capable of tokenizing a URI into it's components.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 0.0.9
 */
class UriScanner extends AbstractScanner
{
    /**
     * an object that is capable of reading characters from a string.
     *
     * @var Reader
     */
    private $reader;

    /**
     * a flag that determines if this scanner has already tokenized the string.
     *
     * @var bool
     */
    private $isScanned;

    /**
     * arary consisting of tokens that were found.
     *
     * @var array
     */
    private $tokens = array();

    /**
     * Create a new scanner to analyse the given uri.
     *
     * @param string $uri the URI that will be scanned.
     * @throws IOException if the given argument is not of type 'string'.
     */
    public function __construct($uri)
    {        
        $this->setReader(new StringReader($uri));
    }
    
    /**
     * {@inheritDoc}
     */
    public function scan()
    {
        if ($this->isScanned()) {
            return $this->getTokens();
        }
        
        $tokens = $this->tokenize();
        
        var_dump($tokens[0]->getValue());
    }
    
    /**
     * Returns an array of tokens by performing an lexical analysis on a sequence of characters.
     *
     * @return array a collection of tokens created by analyzing a sequence of characters.
     */
    private function tokenize()
    {
        $reader = $this->getReader();
        
        // determine if URI is absolute.
        if ($token = $this->tokenizeScheme($reader)) {
            $this->addToken($token);
            
            // determine if URI is hierarhical.
            if($reader->peek(1) === '/') {
                $this->tokenizeHierarchical($reader);
            }
        } else {
            // a relative URI is always hierarhical.
            $this->tokenizeHierarchical($reader);
        }
        
        return $this->getTokens();
    }
    
    /**
     * Returns a token for the scheme name of an URI.
     *
     * @return TokenInterface|null a token for the scheme name, or null if no scheme was found.
     */
    private function tokenizeScheme(Reader $reader)
    {
        // mark the reader so it can be reset.
        $reader->mark();
        
        $token = null;
        $scheme = '';
        while (($char = $reader->readChar()) !== null) {
                // characters that should never occur in a scheme name.
                $forbidden = array('/', '?','#');
                if (in_array($char, $forbidden)) {
                    // reset reader to most recent mark.
                    $this->reset();
                    // stop consuming characters.
                    break;
                }
                
                // found a scheme name.
                if ($char === ':') {
                    // create a token for the scheme name.
                    $token = new Token('URI_SCHEME_NAME', $scheme);
                    // stop consuming characters.
                    break;
                }
                
                // store current character.
                $scheme .= $char; 
        }
        
        return $token;
    }
    
    private function tokenizeHierarchical(Reader $reader)
    {
        // skip if present two forward slashes.
        if ($reader->peek(2) === '//') {
            $reader->skip(2);
        }
        
        var_dump($reader->peek(3));
        
        while (($char = $reader->readChar()) !== null) {

        }
    }
    
    /**
     * Returns an array with tokens for the authority part of an URI.
     *
     * @return array zero or more tokens for the authority part.
     */
    private function tokenizeAuthority(Reader $reader)
    {        

        
        
        $tokens = array();
        $chars = '';
        while (($char = $reader->readChar()) !== null) {

        }
        
    }
    
    private function tokenizePath(Reader $reader)
    {
            
    }
    
    /**
     * Determine whether this scanner has already tokenized the string.
     *
     * @return bool true if the string has already been tokenized, false otherwise.
     */
    public function isScanned()
    {
        return $this->isScanned;
    }
    
    /**
     * Returns the reader used to process a sequence of characters.
     *
     * @return Reader a reader capable of processing a sequence of characters.
     */
    protected function getReader()
    {
        return $this->reader;
    }
    
    /**
     * Set a reader used to process a sequence of characters.
     *
     * @param Reader $reader a reader capable of processing a sequence of characters.
     */
    protected function setReader(Reader $reader)
    {
        if ($reader === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a Reader object as argument; received "null"',
                __METHOD__
            ));
        }
    
        $this->reader = $reader;
    }
    
    /**
     * Store a newly created token.
     *
     * @param TokenInterface $token the token to store.
     * @throws InvalidArgumentException if the given argument is a 'null' literal.
     */
    private function addToken(TokenInterface $token) 
    {
        if ($token === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an object that implements the TokenInterface; received "null"',
                __METHOD__
            ));
        }
        
        $this->tokens[] = $token;
    }
    
    /**
     * Determine whether the scanner has found a token with the given identity.
     *
     * @param string $identity a name by which a token can be identified.
     * @return bool true if a token exists with the given identity, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     * @see TokenInterface::identify()
     */
    private function hasToken($identity) 
    {
	    if (!is_string($identity)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($identity) ? get_class($identity) : gettype($identity))
            ));
	    }
    
        $hasToken = false;

        $tokenCount = size($this->tokens);
        $tokenIndex = 0; 
        while (($tokenIndex < $tokenCount) && !$hasToken) {
            $hasToken = ($this->tokens[$tokenIndex]->identify() == $identity);
            $tokenIndex++;
        }
                
        return $hasToken;
    }
    
    /**
     * Returns all tokens found by the scanner.
     *
     * @return array zero or more tokens found by the scanner.
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}
