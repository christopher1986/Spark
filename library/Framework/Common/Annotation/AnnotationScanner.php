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

namespace Framework\Common\Annotation;

use Framework\Common\Annotation\Exception\MalformedDocBlockException;
use Framework\Io\Reader;
use Framework\Io\StringReader;
use Framework\Io\CachedReader;
use Framework\Scanner\AbstractScanner;
use Framework\Scanner\TokenCollection;
use Framework\Scanner\TokenInterface;
use Framework\Scanner\Token;
use Framework\Util\Strings;

/**
 * The AnnotationScanner is capable of tokenizing annotations that are declared within documentation comments.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class AnnotationScanner extends AbstractScanner
{
    /**
     * Symbolic name for a annotation name.
     * 
     * var int
     */
    const T_ANNOTATION_NAME = 100;
    
    /**
     * Symbolic name for a parameter value.
     * 
     * var int 
     */
    const T_PARAMS = 200;

    /**
     * A reader capable of reading characters from a stream.
     *
     * @var CachedReader
     */
    private $reader;

    /**
     * a collection of tokens found within a stream of text.
     *
     * @var TokenCollection
     */
    private $tokens;

    /**
     * Create a new scanner to analyse the string that contains the documentation comments.
     *
     * @param string $docComment the string containing documentation comments.
     * @throws IOException if the given argument is not of type 'string'.
     */
    public function __construct($docComment)
    {
        $this->setReader(new StringReader($docComment));
    }
    
    /**
     * {@inheritDoc}
     */
    public function scan()
    {    
        $reader = $this->getReader();
        
        static $contextDocBlock = 0x01;
        static $contextClass    = 0x02;
        static $contextParams   = 0x04;
        
        // count number of parentheses found.
        $parenthesesCount = 0;
        // store characters found by reader.
        $readChars = '';
        
        do {            
            /**
             * Collect characters that form a parameter.
             */
            if ($this->hasContext($contextParams)) {
                // store word.
                $readChars .= $reader->getWord();
                // recalculate parentheses.
                $parenthesesCount += substr_count($reader->getWord(), '(');
                $parenthesesCount -= substr_count($reader->getWord(), ')');
                
                // end of parameter list found.
                if ($parenthesesCount === 0) {
                    $this->addToken(new Token(self::T_PARAMS, $readChars));
                    // empty stored characters.
                    $readChars = '';
                    // remove context.
                    $this->removeContext($contextParams);
                }
                
                // consume character.
                $isConsuming = $reader->consumeWord();
                // skip current iteration.
                continue;
            }
        
            /**
             * Collect characters that form the tag name.
             */
            if ($this->hasContext($contextClass)) {
                // end of tag name found.
                if (in_array($reader->getChar(), array(' ', '(')) || $reader->peek(1) === null) {
                    $this->addToken(new Token(self::T_ANNOTATION_NAME, $readChars));
                    // empty stored characters.
                    $readChars = '';
                    // remove context.
                    $this->removeContext($contextClass);
                } else {
                    // still collecting characters.
                    $readChars .= $reader->getChar();
                }
                
                // beginning of parameter list found.
                if ($reader->getChar() === '(') {
                    $this->addContext($contextParams);
                    // skip current iteration.
                    continue;
                }
                
                // consume character.
                $isConsuming = $reader->consumeChar();
                // skip current iteration.
                continue;
            }

            /**
             * Consume characters until a tag name is found.
             */
            if ($this->hasContext($contextDocBlock)) { 
                // beginning of tag name found.
                if ($reader->getChar() === '@') {
                    $this->addContext($contextClass);
                }

                // characters that are allowed to precede a tag name.
                if (ctype_space($reader->getChar()) || in_array($reader->getChar(), array('*', '@'))) {
                    // consume character.
                    $isConsuming = $reader->consumeChar();
                    // skip current iteration.
                    continue; 
                }
            }
            
            /**
             * Set context to docblock.
             */
            if ($reader->getWord() === '/**') {
                // add doblock to context.
                $this->addContext($contextDocBlock);
                // consume word.
                $isConsuming = $reader->consumeWord();
                // skip current iteration.
                continue;
            }   
                     
            // consume the current line.
            $isConsuming = $reader->consumeLine();
        } while ($isConsuming);

        return $this->getTokens();
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
    
        $this->reader = new CachedReader($reader);
    }
    
    /**
     * Returns the reader used to process a sequence of characters.
     *
     * @return CachedReader a reader capable of processing a sequence of characters.
     */
    protected function getReader()
    {
        return $this->reader;
    }
    
    /**
     * Store a newly created token.
     *
     * @param TokenInterface $token the token to store.
     */
    private function addToken(TokenInterface $token) 
    {        
        $this->getTokens()->add($token);
    }
    
    /**
     * Returns if present the last token that was added to the collection.
     *
     * @return TokenInterface|null the last token added to the collection, or null if collection is emtpy.
     */
    private function getLastToken()
    {
        $tokens = $this->getTokens();
        return (!$tokens->isEmpty()) ? end($tokens) : null;
    }
    
    /**
     * Returns a collection of tokens found by the scanner.
     *
     * @return array a collection of tokens.
     */
    private function getTokens()
    {
        if ($this->tokens === null) {
            $this->tokens = new TokenCollection();
        }
        return $this->tokens;
    }
}
