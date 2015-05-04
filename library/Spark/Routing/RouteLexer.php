<?php
/**
 * Copyright (c) 2015, Chris Harris.
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
 * @copyright  Copyright (c) 2015 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Routing;

use Spark\Collection\ArrayList;
use Spark\Collection\Exception\NoSuchElementException;
use Spark\Parser\Lexer\Token;
use Spark\Parser\Lexer\TokenInterface;

/**
 * The RouteLexer is also known as a text scanner which converts a sequence of characters 
 * into a sequence of tokens. This scanner will tokenize the string representation of a route 
 * into a collection of tokens which can then be further analyzed by a parser.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class RouteLexer
{
    /**
     * Token: none symbol
     *
     * @var int
     */
    const T_NONE = 1;

    /**
     * Token: string type
     *
     * @var int
     */
    const T_STRING = 2;

    /**
     * Token: parameter
     *
     * @var int
     */
    const T_PARAMETER = 100;
    
    /**
     * Token: open bracket
     *
     * @var int
     */
    const T_OPEN_BRACKET = 101;
    
    /**
     * Token: close bracket
     *
     * @var int
     */
    const T_CLOSE_BRACKET = 102;

    /**
     * Values that are not case sensitive.
     *
     * @var array
     */
    private $noCase = array(
        '[' => self::T_OPEN_BRACKET,
        ']' => self::T_CLOSE_BRACKET
    );

    /**
     * A collection of tokens found by the scanner.
     *
     * @var ArrayList
     */
    public $tokens;

    /**
     * Index of the current token.
     *
     * @var TokenInterface
     */
    public $tokenIndex = 0;

    /**
     * Returns a collection of regular expressions whose result will be captured.
     *
     * @return array a collection of regular expressions that will be captured.
     * @link http://www.regular-expressions.info/lookaround.html Positive and Negative Lookahead
     */
    private function getCatchablePatterns()
    {
        return array(
            '(?<!\\\)[\[\]]',
            ':[a-z_\x7f-\xff]{1}[a-z0-9_\x7f-\xff]*',
        );
    }
    
    /**
     * Set the input to scan.
     *
     * Calling this operation will reset the scanner; meaning that the results of a previous
     * scan operation are replaced by new results.
     *
     * @param string $input the route to scan.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    public function setInput($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        }
    
        $this->reset();
        $this->scan($input);       
    }
    
    /**
     * Returns a token that lies beyond the "current" token without moving the internal pointer forward.
     *
     * @param int $lookahead the number of tokens to look forward.
     * @return null|mixed the token found, or null if lookahead is larger than the number of tokens in this collection.
     * @throws InvalidArgumentException if the given argument is not an integer value.
     */
    public function peek($lookahead = 1)
    {
	    if (!is_int($lookahead)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($lookahead) ? get_class($lookahead) : gettype($lookahead))
            ));
	    }

        $token = null;
        if (($index = $this->tokens->key()) >= 0 && is_int($index)) {
            $index = (int) ($index + $lookahead);
            if ($index < $this->tokens->count()) {
                $token = $this->tokens->get($index);
            }
        }
        return $token;
    }
    
    /**
     * Returns the next token from the scanner.
     *
     * @return TokenInterface the next token.
     * @throws NoSuchElementException if there are no tokens left.
     */
    public function next()
    {     
        if ($this->tokenIndex < $this->tokens->count()) {
            $this->tokenIndex++;
        }
        return $this->current();
    }
    
    /**
     * Returns the current token from the scanner.
     *
     * @return TokenInterface the last token retrieved from the scanner.
     */
    public function current()
    {        
        $token = null;
        if ($this->tokenIndex < $this->tokens->count()) {
            $token = $this->tokens->get($this->tokenIndex);
        }
        return $token;
    }
        
    /**
     * Converts the given sequence of characters into a sequence of tokens.
     */
    private function scan($input)
    {           
        static $regex;  
        if (!isset($regex)) {
            $regex = '/(' . implode(')|(', $this->getCatchablePatterns()) . ')/i';
        }

        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($regex, $input, -1, $flags);
        
        foreach ($matches as $match) {
            $type = $this->getType($match[0]);
            $this->tokens->add(new Token($type, $match[0], $match[1]));
        }
    }
    
    /**
     * Reset scanner to starting state.
     *
     * @return void
     */
    private function reset()
    {
        $this->tokens = new ArrayList();
        $this->tokenIndex = 0;
    }
    
    /**
     * Returns a token type for the given value.
     *
     * @param string $value the value whose token type is to be determined.
     * @return int a token type for the given value.
     * @link http://docstore.mik.ua/orelly/webprog/php/ch02_01.htm#progphp-CHP-2-SECT-1.6 PHP Identifiers
     */
    private function getType($value)
    {    
        /**
         * ASCII 8-bit characters are valid in a PHP identifier.
         */
        if (preg_match('/^:[a-z_\x7f-\xff]{1}[a-z0-9_\x7f-\xff]*$/i', $value)) {
            return self::T_PARAMETER;
        }
        
        if (isset($this->noCase[$value])) {
            return $this->noCase[$value];
        }
        
        if (!empty($value)) {
            return self::T_STRING;
        }
        
        return self::T_NONE;   
    }
}
