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

    private $docComment;

    /**
     * Create a new scanner to analyse the string that contains the documentation comments.
     *
     * @param string $docComment the string containing documentation comments.
     * @throws IOException if the given argument is not of type 'string'.
     */
    public function __construct($docComment)
    {
        $this->docComment = $docComment;
    }
    
    const T_NONE                = 1;
    const T_INTEGER             = 2;
    const T_STRING              = 3;
    const T_FLOAT               = 4;

    // All tokens that are also identifiers should be >= 100
    const T_IDENTIFIER          = 100;
    const T_AT                  = 101;
    const T_CLOSE_CURLY_BRACES  = 102;
    const T_CLOSE_PARENTHESIS   = 103;
    const T_COMMA               = 104;
    const T_EQUALS              = 105;
    const T_FALSE               = 106;
    const T_NAMESPACE_SEPARATOR = 107;
    const T_OPEN_CURLY_BRACES   = 108;
    const T_OPEN_PARENTHESIS    = 109;
    const T_TRUE                = 110;
    const T_NULL                = 111;
    const T_COLON               = 112;

    protected $noCase = array(
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

    protected $withCase = array(
        'true'  => self::T_TRUE,
        'false' => self::T_FALSE,
        'null'  => self::T_NULL
    );

    
    /**
     * {@inheritdoc}
     */
    protected function getCatchablePatterns()
    {
        return array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z]{1}',
            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
            '"(?:[^"]|"")*"',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+', '\*+', '(.)');
    }
    
    /**
     * {@inheritdoc}
     *
     * @param string $value
     *
     * @return int
     */
    protected function getType(&$value)
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
            return (strpos($value, '.') !== false || stripos($value, 'e') !== false)
                ? self::T_FLOAT : self::T_INTEGER;
        }

        return $type;
    }
    
    /**
     * {@inheritDoc}
     */
    public function scan()
    {    
        static $regex;

        if ( ! isset($regex)) {
            $regex = '/(' . implode(')|(', $this->getCatchablePatterns()) . ')|'
                   . implode('|', $this->getNonCatchablePatterns()) . '/i';
        }

        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($regex, $this->docComment, -1, $flags);

        foreach ($matches as $match) {
            // Must remain before 'value' assignment since it can change content
            $type = $this->getType($match[0]);

            $this->addToken(new Token($type, $match[0]));
        }

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
