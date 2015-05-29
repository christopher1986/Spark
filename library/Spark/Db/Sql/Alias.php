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

namespace Spark\Db\Sql;

/**
 * The As class represents an alias for a SQL identifier. To accomplish that purpose
 * it decorates an object that implements the {@link IdentifierInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Alias 
{   
    /**
     * The identifier.
     *
     * @var IdentifierInterface
     */
    private $identifier;
    
    /**
     * The alias.
     *
     * @var string
     */
    private $alias = '';

    /**
     * Create a new alias.
     *
     * @param string $identifier the identifier for which this alias is created.
     * @paration string $alias the alias.
     */
    public function __construct(IdentifierInterface $identifier, $alias)
    {
        $this->setIdentifier($identifier);
        $this->setAlias($alias);
    }

    /**
     * Set the identifier this alias represents.
     *
     * @param IdentifierInterface $identifier the identifier this alias represents.
     */
    public function setIdentifier(IdentifierInterface $identifier)
    {    
        $this->identifier = $identifier;
    }
    
    /**
     * Returns the identifier this alias represents.
     *
     * @var IdentifierInterface the identifier.
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * Set the alias name.
     *
     * @param string $alias the alias.
     * @throws InvalidArgumentException if the given argument is not a 'string' type.
     */
    public function setAlias($alias)
    {
	    if (!is_string($alias)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($alias)) ? get_class($alias) : gettype($alias)
            ));
	    }
    
        $this->alias = $alias;
    }
    
    /**
     * Returns the alias.
     *
     * @return string the alias.
     */
    public function getAlias()
    {
        return $this->alias;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        return sprintf('%s AS %s', (string) $this->getIdentifier(), $this->getAlias());
    }
}
