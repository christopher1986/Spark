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
 * The From class represents the table to retrieve results from. To accomplish that purpose
 * it decorates an object that implements the {@link IdentifierInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class From
{   
    /**
     * The identifier.
     *
     * @var IdentifierInterface
     */
    private $table;
    
    /**
     * The alias.
     *
     * @var string
     */
    private $alias = '';

    /**
     * Create a new alias.
     *
     * @param string $table the table name.
     * @paration string $alias (optional) the alias.
     */
    public function __construct($table, $alias = '')
    {
        $this->setTable($table);
        $this->setAlias($alias);
    }

    /**
     * Set the table name.
     *
     * @param string $table the table name
     */
    public function setTable($table)
    {    
	    if (!is_string($table)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($table)) ? get_class($table) : gettype($table)
            ));
	    }
    
        $this->table = $table;
    }
    
    /**
     * Returns the table name.
     *
     * @return string the table name.
     */
    public function getTable()
    {
        return $this->table;
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
        $from = sprintf('FROM %s', $this->getTable());
        if (($alias = $this->getAlias()) !== '') {
            $from = sprintf('%s %s', $from, $alias);
        }
    
        return $from;
    }
}
