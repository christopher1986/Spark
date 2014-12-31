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

namespace Framework\Scanner;

class PhpScanner extends AbstractScanner
{
    /**
     * Symbolic name for a namespace.
     * 
     * var int
     */
    const T_NAMESPACE = 100;
    
    /**
     * Symbolic name for a use statement.
     * 
     * var int 
     */
    const T_USE_STATEMENT = 200;
    
    /**
     * Symbolic name for an as statement.
     * 
     * var int 
     */
    const T_AS_STATEMENT = 201;
    
    /**
     * Symbolic name for a include statement.
     * 
     * var int  
     */
    const T_INCLUDE_STATEMENT = 300;
    
    /**
     * Symbolic name for a class name. 
     *
     * var int
     */
    const T_CLASS_NAME = 400;

    /**
     * The content to tokenize.
     *
     * @var string
     */
    private $content;
    
    /**
     * Array consisting of tokens that were found.
     *
     * @var array
     */
    private $tokens;
    
    /**
     * A flag to indicate if scanning has occurred.
     *
     * @var bool
     */
    private $isScanned = false;
    
    /**
     * Create a TokenScanner.
     *
     * @param string $content the file content to tokenize.
     */
    public function __construct($content)
    {
        $this->setContent($content);
    }
    
    /**
     * Set the file content that will be tokenized.
     *
     * @param string $content the file content to tokenize.
     * @throws InvalidArgumentException if the given argument is not of type string.
     */
    private function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($content) ? get_class($content) : gettype($content))
            ));
        }
        
        $this->content = $content;
    }
    
    /**
     * Returns the content that will be tokenized.
     *
     * @return string the content to tokenize.
     */
    private function getContent()
    {
        return $this->content;
    }
    
    /**
     * {@inheritDoc}
     */
    public function scan()
    {    
        if ($this->isScanned) {
            return $this->tokens;
        }
        
        static $contextNamespace = 0x01;
        static $contextImporting = 0x02;
        static $contextAliasing  = 0x04;
        static $contextInclude   = 0x08;
        static $contextClass     = 0x10;
        
        $value = '';
        
        $phpTokens = $this->tokenize();
        foreach ($phpTokens as $phpToken) {
            // store array values as variables.
            list($identifier, $content, $lineNumber) = $phpToken;
            
            /**
             * Collect the content of all tokens to form a class name.
             */
            if ($this->hasContext($contextClass)) {
                if ($identifier === T_STRING) {
                    $value .= $content;
                }
                
                if ($identifier === null && $content === '{') {                
                    $this->addToken(new Token(self::T_CLASS_NAME, $value));
                    $value = '';
                    $this->removeContext($contextClass);  
                }
            }
            
            /**
             * Collect the content of all tokens to form an include statement.
             */
            if ($this->hasContext($contextInclude)) {
                if ($identifier === T_CONSTANT_ENCAPSED_STRING) {
                    $value .= $content;
                }
                
                // a semicolon indicates the end of an include statement.
                if ($identifier === null && $content === ';') {
                    $this->addToken(new Token(self::T_INCLUDE_STATEMENT, $value));
                    $value = '';
                    $this->removeContext($contextInclude);
                }
            }
            
            /**
             * Collect the content of all tokens to form an aliasing statement.
             */
            if ($this->hasContext($contextAliasing)) {
                if ($identifier === T_STRING) {
                    $value .= $content;
                }
                
                // a semicolon or comma indicates the end of alias statement.
                if ($identifier === null && ($content === ';' || $content === ',')) {
                    $this->addToken(new Token(self::T_AS_STATEMENT, $value));
                    $value = '';
                    $this->removeContext($contextAliasing);
                    
                    // set context to importing again.
                    if ($content === ',') {
                        $this->setContext($contextImporting);
                        continue;
                    }
                }
            }
            
            /**
             * Collect the content of all tokens to form a use statement.
             */
            if ($this->hasContext($contextImporting)) {   
                if ($identifier === T_NS_SEPARATOR || $identifier === T_STRING) {
                    $value .= $content;
                }
                
                // set context to aliasing.
                if ($identifier === T_AS) {
                    $this->addToken(new Token(self::T_USE_STATEMENT, $value));
                    $value = '';
                    $this->setContext($contextAliasing);
                }
                
                // use semicolon and comma as delimiters for a single use statement.
                if ($identifier === null && ($content === ';' || $content === ',')) {
                    $this->addToken(new Token(self::T_USE_STATEMENT, $value));
                    $value = '';
                    
                    // a semicolon indicates the end of use statements.
                    if ($content === ';') {
                        $this->removeContext($contextImporting);
                    }
                }
            }            
            
            /**
             * Collect the content of all tokens to form a namespace.
             */
            if ($this->hasContext($contextNamespace)) {
                if ($identifier === T_NS_SEPARATOR || $identifier === T_STRING) {
                    $value .= $content;
                }
                
                // a semicolon or curly bracket indicates the end of a namespace.
                if ($identifier === null && ($content === ';' || $content === '{')) {
                    $this->addToken(new Token(self::T_NAMESPACE, $value));
                    $value = '';
                    $this->removeContext($contextNamespace);
                }
            }
            
            // change context to namespace.
            if ($identifier === T_NAMESPACE && $this->isContextFree()) {
                $this->setContext($contextNamespace);
            }
            
            // change context to importing.
            if ($identifier === T_USE && $this->isContextFree()) {
                $this->setContext($contextImporting);
            }
            
            // change context to include.
            if (($identifier === T_REQUIRE || $identifier === T_REQUIRE_ONCE ||
                 $identifier === T_INCLUDE || $identifier === T_INCLUDE_ONCE) && $this->isContextFree()) {
                $this->setContext($contextInclude);
            }
            
            // change context to class.
            if ($identifier === T_CLASS && $this->isContextFree()) {
                $this->setContext($contextClass);
            }
        }
        
        $this->isScanned = true;
        
        return $this->tokens;
    }
    
    /**
     * Returns an array of PHP language tokens using the Zend engine's lexical scanner.
     *
     * @param bool $retokenize if provided will tokenize the content again.
     * @return array a collection of PHP language tokens.
     * @link http://php.net/manual/en/function.token-get-all.php
     */
    private function tokenize()
    {
        $tokens = array();
        if ($rawTokens = token_get_all($this->getContent())) {
            foreach ($rawTokens as $token) {
                $tokens[] = (is_array($token)) ? $token : array(null, $token, null);
            }
        }
        return $tokens;
    }
    
    /**
     * Store a newly created token.
     *
     * @param TokenInterface $token the token to store.
     */
    private function addToken(TokenInterface $token)
    {
        $this->tokens[] = $token;
    }
}
