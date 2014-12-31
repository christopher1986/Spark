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

/**
 * A token stores meaningful character strings that are found when peforming lexical analysis.
 * 
 * A token should consists of a name by which it can be identified and an optional value. The name of
 * a token does not have to be unique amongst other tokens. The name of a token is simply used to hint 
 * what value is stored by the token. The value stored by a token can be of any type, but it's most 
 * likely that a token is used to stored a sequence of characters found with a lexer.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Token implements TokenInterface
{
    /**
     * A name that identifies this token.
     *
     * @var string
     */
    private $identity;

    /**
     * The value stored by this token.
     * 
     * @var mixed
     */
    private $value;

    /**
     * Create a new token.
     *
     * @param string $identity a name that identifies this token.
     * @param mixed|null $value a value that will be stored by this token.
     */
    public function __construct($identity, $value = null)
    {
        $this->setIdentity($identity);
        $this->setValue($value);
    }
    
    /**
     * Set a name to identify this token.
     *
     * It's not uncommon for tokens to share the same identity. It's the value stored by a token that makes it's 
     * unique amongst other tokens. The name of a token is simply used to hint what value is stored by the token.
     *
     * @param mixed $identity a value that identifies this token.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function setIdentity($identity)
    {        
        $this->identity = $identity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function identify()
    {
        return $this->identity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
