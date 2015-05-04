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

use Spark\Parser\Exception\ParserSyntaxException;
use Spark\Parser\Lexer\TokenInterface;
use Spark\Routing\Ast\NodeInterface;
use Spark\Routing\Ast\Node\Route;
use Spark\Routing\Ast\Node\Optional;
use Spark\Routing\Ast\Node\Parameter;
use Spark\Routing\Ast\Node\Text;


/**
 * The RouteParser performs syntactic analysis on a route. The given route is first tokenized
 * into symbols. The tokens created during the tokenization process are then validated against 
 * a set of production rules.
 *
 * The following production rules expressed in Extended Backus-Naur-Form (EBNF) illustrate the 
 * context-free grammar for a route.
 *
 * Path       ::= Text | Optional | Parameter
 * Optional   ::= "[" {Path | "/"} "]"
 * Parameter  ::= ":" Text
 * Text       ::= string
 *
 * The lowercase values represent terminal symbols (also known as token types) which are 
 * created in a process called tokenization which is done by a lexer or scanner.
 * 
 * @author Chris Harris
 * @version 1.0.0
 */
class RouteParser
{
    /**
     * A lexer to tokenize the input string.
     *
     * @var RouteLexer
     */
    private $lexer;

    /**
     * Construct the parser.
     */
    public function __construct()
    {
        $this->lexer = new RouteLexer();
    }

    /**
     * Parse the given input string.
     *
     * @param string $input the route to parse.
     * @return string a regular expression for the parsed input string. 
     */
    public function parse($input)
    {
        $this->lexer->setInput($input);        
        
        $nodes = array();
        while (($token = $this->lexer->current()) !== null) {
            $nodes[] = $this->path();
        }

        $tree = new Route();
        $tree->setChildren($nodes);

        return $tree;
    }
    
    /**
     * Parse the token(s) that belongs to a path.
     *
     * @return NodeInterface a node that represents the current path.
     */ 
    private function path()
    {
        $token = $this->lexer->current();
        if ($token === null) {
            throw new ParserSyntaxException(sprintf(
                '%s(): unexpected end of route.',
                __CLASS__
            ));
        }
    
        switch($token->identify()) {
            case RouteLexer::T_OPEN_BRACKET:
                return $this->optional();
            case RouteLexer::T_PARAMETER:
                return $this->parameter();
            case RouteLexer::T_STRING:
                return $this->text();
        }

        // consume token.
        $this->lexer->next();
    }
        
    /**
     * Parse all tokens within an optional (non-capturing) group.
     *
     * @return Optional a node containing optional parts of a route.
     */
    private function optional()
    {         
        $token = $this->lexer->current();  
        if ($token === null || !$this->match($token, RouteLexer::T_OPEN_BRACKET)) {
            $this->syntaxError('"[" (T_OPEN_BRACKET)');
        }

        // consume token.
        $this->lexer->next();

        $nodes = array();
        while (!$this->match($this->lexer->current(), RouteLexer::T_CLOSE_BRACKET)) {      
            $nodes[] = $this->path();
            
            /*
             * A sudden end of tokenss means that a T_CLOSE_BRACKET token
             * is missing which is considered a syntax error.
             */
            if ($this->lexer->current() === null) {
                $this->syntaxError('"]" (T_CLOSE_BRACKET)');
            }
        }
        
        // consume token.
        $this->lexer->next();

        return new Optional($nodes);
    }
    
    /**
     * Parse a single parameter token.
     *
     * @return Parameter a node containing the parameter name.
     */
    private function parameter()
    {
        $token = $this->lexer->current();
        if ($token === null || !$this->match($token, RouteLexer::T_PARAMETER)) {
            $this->syntaxError('parameter (T_PARAMETER)');
        }
        
        // consume token.
        $this->lexer->next();
        
        return new Parameter($token->getValue());
    }
    
    /**
     * Parse a single text token.
     *
     * @return Text a node containing a text string.
     */
    private function text()
    {
        $token = $this->lexer->current();
        if ($token === null) {
            throw new ParserSyntaxException(sprintf(
                '%s(): unexpected end of route.',
                __CLASS__
            ));
        } else if ($token->identify() !== RouteLexer::T_STRING) {
            $this->syntaxError('text');
        }

        // consume token.
        $this->lexer->next();
        
        return new Text($token->getValue());
    }
    
    /**
     * Returns true if the given token matches any of the token types.
     *
     * @param TokenInterface $token the token whose type will be tested.
     * @param array|Traversable $types a collection of possible token types to match.
     * @param bool $strict if true strict comparison will be performed on the token type.
     * @return bool true if the given token matches at least one token type, false otherwise.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    private function matchAny(TokenInterface $token, $types, $strict = true) 
    {
        if (!is_array($types) && !($types instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s(): expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($types) ? get_class($types) : gettype($types))
            ));
        }
    
        if ($types instanceof \Traversable) {
            $types = iterator_to_array($types);
        }
    
        return in_array($token->identify(), $types, (bool) $strict);
    }
    
    /**
     * Returns true if the given token matches the given token type. 
     *
     * @param TokenInterface $token the token whose type will be tested.
     * @param mixed $type the token type to match.
     * @param bool $strict if true strict comparison will be performed on the token type.
     * @return bool true if the given token matches with the given token type, false otherwise.
     */
    private function match(TokenInterface $token, $type, $strict = true)
    {
        if ((bool) $strict) {
            return ($token->identify() === $type);
        } else {
            return ($token->identify() == $type);
        }
    }
    
    /**
     * Creates and throws a detailed error message if a syntax error is encountered.
     *
     * @param string $expected the expected token.
     * @param TokenInterface $token (optional) the token that was received instead. 
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    private function syntaxError($expected, TokenInterface $token = null)
    {
        if (!is_string($expected)) {
            throw new \InvalidArgumentException(sprintf(
                '%s(): expects a string argument; received "%d"',
                __METHOD__,
                (is_object($expected) ? get_class($expected) : gettype($expected))
            ));
        }
    
        if ($token === null) {
            $token = $this->lexer->current();
        }
        
        $message = sprintf('%s: expected %s, received ', __CLASS__, $expected);
        if ($token === null) {
            $message .= 'end of string';
        } else {
            $message .= sprintf('"%s" at position %s', $token->getValue(), $token->getPosition());
        }
        
        throw new ParserSyntaxException($message);
    }
}
