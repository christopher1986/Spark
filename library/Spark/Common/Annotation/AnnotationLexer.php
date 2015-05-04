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

namespace Spark\Common\Annotation;

use Spark\Collection\ArrayList;
use Spark\Collection\Exception\NoSuchElementException;
use Spark\Scanner\AbstractScanner;
use Spark\Parser\Lexer\Token;
use Spark\Util\Strings;

/**
 * The AnnotationLexer converts a sequence of characters into a sequence of tokens.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class AnnotationLexer
{
    /**
     * Token: none symbol
     *
     * @var int
     */
    const T_NONE = 1;

    /**
     * Token: integer type
     *
     * @var int
     */
    const T_INTEGER = 2;
    
    /**
     * Token: string type
     *
     * @var int
     */
    const T_STRING = 3;
    
    /**
     * Token: float type
     *
     * @var int
     */
    const T_FLOAT = 4;

    /**
     * Token: identifier symbol
     *
     * @var int
     */
    const T_IDENTIFIER = 100;
    
    /**
     * Token: at symbol
     *
     * @var int
     */
    const T_AT = 101;
    
    /**
     * Token: close curly brace
     *
     * @var int
     */
    const T_CLOSE_CURLY_BRACES = 102;
    
    /**
     * Token: close parenthesis
     *
     * @var int
     */
    const T_CLOSE_PARENTHESIS = 103;
    
    /**
     * Token: comma symbol
     *
     * @var int
     */
    const T_COMMA = 104;
    
    /**
     * Token: equals symbol
     *
     * @var int
     */
    const T_EQUALS = 105;
    
    /**
     * Token: boolean false
     *
     * @var int
     */
    const T_FALSE = 106;
    
    /**
     * Token: namespace separator symbol
     *
     * @var int
     */
    const T_NAMESPACE_SEPARATOR = 107;
    
    /**
     * Token: open curly brace
     *
     * @var int
     */
    const T_OPEN_CURLY_BRACES = 108;
    
    /**
     * Token: open parenthesis
     *
     * @var int
     */
    const T_OPEN_PARENTHESIS = 109;
    
    /**
     * Token: boolean true
     *
     * @var int
     */
    const T_TRUE = 110;
    
    /**
     * Token: NULL literal
     *
     * @var int
     */
    const T_NULL = 111;
    
    /**
     * Token: colon symbol
     *
     * @var int
     */
    const T_COLON = 112;
    
    /**
     * Values that are not case sensitive.
     *
     * @var array
     */
    private $noCase = array(
        '@'  => self::T_AT,
        ','  => self::T_COMMA,
        '('  => self::T_OPEN_PARENTHESIS,
        ')'  => self::T_CLOSE_PARENTHESIS,
        '{'  => self::T_OPEN_CURLY_BRACES,
        '}'  => self::T_CLOSE_CURLY_BRACES,
        '='  => self::T_EQUALS,
        ':'  => self::T_COLON,
        '\\' => self::T_NAMESPACE_SEPARATOR
    );
    
    /**
     * Values that are case sensitive.
     *
     * @var array
     */
    private $withCase = array(
        'true'  => self::T_TRUE,
        'false' => self::T_FALSE,
        'null'  => self::T_NULL
    );

    /**
     * The input string to analyse.
     *
     * @var string
     */
    private $input;

    /**
     * A collection of tokens.
     *
     * @var ArrayList
     */
    private $tokens;

    /**
     * Last token retrieved from the scanner.
     *
     * @var TokenInterface
     */
    private $token;

    /**
     * Construct a scanner to analyse the given input string.
     *
     * @param string $input the input string to analyse.
     */
    public function __construct($input)
    {
        $this->input = $input;
        $this->tokens = $this->scan($input);
    }
    
    /**
     * Converts the given sequence of characters into a sequence of tokens.
     *
     * @return ArrayList a collection of tokens.
     */
    private function scan($input)
    {        
        static $regex;
        
        if (!isset($regex)) {
            $regex = '/(' . implode(')|(', $this->getCatchablePatterns()) . ')|'
                   . implode('|', $this->getNonCatchablePatterns()) . '/i';
        }

        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($regex, $input, -1, $flags);

        $tokens = new ArrayList();
        foreach ($matches as $match) {
            // must remain before 'value' assignment since it can change content
            $type = $this->getType($match[0]);
            $tokens->add(new Token($type, $match[0]));
        }
        
        return $tokens;
    }
    
    /**
     * Rewinds the scanner to the first token. 
     *
     * A scanner is bound to the input string for which it was intially created. This means that the input string 
     * on which the lexical analyses has taken place will not be discarded by calling this method. To analyse a
     * different string a new Scanner object must be created.
     *
     * @return void
     */
    public function reset()
    {
        reset($this->tokens);
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
        if (!$this->hasNext()) {
            throw new NoSuchElementException(sprintf(
                '%s: no tokens left; use the %s::hasNext() method to determine if there still are tokens left.',
                __METHOD__,
                __CLASS__
            ));
        }
        
        // don't call next on first iteration.
        if ($this->token !== null) {
            $this->tokens->next();
        }
        $this->token = $this->tokens->current();
        
        return $this->current();
    }
    
    /**
     * Returns the current token from the scanner.
     *
     * @return TokenInterface the last token retrieved from the scanner.
     */
    public function current()
    {
        return $this->token;
    }
    
    /**
     * Returns true if the scanner still has tokens left.
     *
     * @return bool true if the scanner still has tokens left, false otherwise.
     */
    public function hasNext()
    {
        $index = $this->tokens->key();
        return (++$index < $this->tokens->count());
    }
    
    /**
     * Returns an array with regular expression that need to be captured.
     *
     * @return array numeric array containing regular expressions to catch.
     */
    private function getCatchablePatterns()
    {
        return array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z]{1}',
            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
            '"(?:[^"]|"")*"',
        );
    }

    /**
     * Returns an array with regular expression that do not need catching.
     *
     * @return array numeric array containing regular expressions not to catch.
     */
    private function getNonCatchablePatterns()
    {
        return array('\s+', '\*+', '(.)');
    }
    
    /**
     * Returns a token type for the given value.
     *
     * @param string $value the value whose type is to be determined.
     * @return int a token type for the given value.
     */
    private function getType(&$value)
    {
        $type = self::T_NONE;

        if ($value[0] === '"') {
            $value = str_replace('""', '"', substr($value, 1, strlen($value) - 2));

            return self::T_STRING;
        }

        if (isset($this->noCase[$value])) {
            return $this->noCase[$value];
        }

        if ($value[0] === '_' || $value[0] === '\\' || ctype_alpha($value[0])) {
            return self::T_IDENTIFIER;
        }

        $lowerValue = strtolower($value);

        if (isset($this->withCase[$lowerValue])) {
            return $this->withCase[$lowerValue];
        }

        // Checking numeric value
        if (is_numeric($value)) {
            return (strpos($value, '.') !== false || stripos($value, 'e') !== false) ? self::T_FLOAT : self::T_INTEGER;
        }

        return $type;
    }
}
