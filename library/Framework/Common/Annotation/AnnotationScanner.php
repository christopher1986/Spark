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
     * A reader capable of reading characters from a stream.
     *
     * @var CachedReader
     */
    private $reader;

    /**
     * arary consisting of tokens that were found.
     *
     * @var array
     */
    private $tokens = array();

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
        $tokens = $this->tokenize();
        foreach ($tokens as $token) {
            if ($token->identify() === 'ANNOTATION_PARAMETER') {
                if (($value = $token->getValue()) && strlen($value) > 0) {
                    $token->setValue(trim($value));
                }
            }
        }
    }
    
    /**
     * Returns an array of tokens by performing an lexical analysis on a sequence of characters.
     *
     * @return array a collection of tokens created by analyzing a sequence of characters.
     */
    private function tokenize()
    {    
        $reader = $this->getReader();
        
        static $contextDocBlock = 0x01;
        static $contextTag      = 0x02;
        static $contextParams   = 0x04;
        
        // count number of parentheses found.
        $parenthesesCount = 0;
        // store characters found by reader.
        $readChars = '';
        
        do {
            /*
             * store all the characters that belong to a parameter and create a token for 
             * the parameter if a comma or parenthesis is encountered.
             */
            if ($this->hasContext($contextParams)) {
                if ($reader->getChar() === '(') {
                    $parenthesesCount++;
                } else if ($reader->getChar() === ')') {
                    $parenthesesCount--;
                }
                
                // remove parameters from context.
                if ($parenthesesCount === 0) {
                    $this->removeContext($contextParams);
                }
                   
                // characters that indicate the end of a parameter.
                if (in_array($reader->getChar(), array(',', ')'))) {
                    $this->addToken(new Token('ANNOTATION_PARAMETER', $readChars));
                    // empty stored characters.
                    $readChars = '';
                } else {
                    // store character.
                    $readChars .= $reader->getChar();
                }
                
                // consume character.
                $isConsuming = $reader->consumeChar();
                // skip current iteration.
                continue;
            }
        
            /*
             * store all the characters that belong to a tag and create a token for the 
             * tag if a parenthesis, whitespace or EOF is encountered.
             */
            if ($this->hasContext($contextTag)) {
                // characters that indicate the end of a docblock tag.
                if (in_array($reader->getChar(), array(' ', '(')) || $reader->peek(1) === null) {
                    $this->addToken(new Token('ANNOTATION_CLASS_NAME', $readChars));
                    // empty stored characters.
                    $readChars = '';
                    // remove tag from context.
                    $this->removeContext($contextTag);
                } else {
                    // store character.
                    $readChars .= $reader->getChar();
                }
                
                if ($reader->getChar() === '(') {
                    // add parameters to context.
                    $this->addContext($contextParams);
                    // set initial parentheses count.
                    $parenthesesCount = 1;
                }
                
                // consume character.
                $isConsuming = $reader->consumeChar();
                // skip current iteration.
                continue;
            }
            
            if ($reader->getWord() === '*/') {
                // remove dockblock from context.
                $this->removeContext($contextDocBlock);
                // consume word.
                $isConsuming = $reader->consumeWord();
                // skip current iteration.
                continue;
            }
            
            /*
             * search for lines within the docblock that start with a tag. Certain characters such 
             * as whitespace and asterisks can precede a tag and are simply consumed.
             */
            if ($this->hasContext($contextDocBlock)) {
                // add tag to context.
                if ($reader->getChar() === '@') {
                    $this->addContext($contextTag);
                }

                // characters that can precede a tag.
                if (ctype_space($reader->getChar()) || in_array($reader->getChar(), array('*', '@'))) {
                    // consume character.
                    $isConsuming = $reader->consumeChar();
                    // skip current iteration.
                    continue; 
                }
            }
            
            if ($reader->getWord() === '/**') {
                // add doblock to context.
                $this->addContext($contextDocBlock);
                // consume word.
                $isConsuming = $reader->consumeWord();
                // skip current iteration.
                continue;
            }   
                     
            // consume line.
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
        $this->tokens[] = $token;
    }
    
    /**
     * Returns if present the last token that was added to the collection.
     *
     * @return TokenInterface|null the last token added to the collection, or null if collection is emtpy.
     */
    private function getLastToken()
    {
        $token = null;
        if (($tokens = $this->getTokens()) && count($tokens) > 0) {
            $token = end($tokens);
        }
        return $token;
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
