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

namespace Spark\Db\Query;

use Spark\Db\Adapter\AdapterAwareInterface;
use Spark\Db\Adapter\AdapterCapableInterface;
use Spark\Db\Adapter\AdapterInterface;
use Spark\Db\Sql\Limit;
use Spark\Db\Sql\Offset;

/**
 *
 *
 * @author Chris Harris
 * @version 0.0.1
 * @since 0.0.1
 */
abstract class AbstractSql implements AdapterAwareInterface, AdapterCapableInterface
{
    /**
     * Indicates that nothing has changed.
     *
     * @var int
     */
    const IS_CLEAN = 0x01;
    
    /**
     * Indicates that the underlying data has changed.
     * 
     * @var int
     */
    const IS_DIRTY = 0x02;

    /**
     * The current state.
     *
     * @var int
     */
    protected $state = self::IS_DIRTY;

    /**
     * A query which might contain placeholders.
     *
     * @var string
     */
    protected $query = '';

    /**
     * The parts that form the insert statement.
     *
     * @var array
     */
    protected $parts = array();

    /**
     * A database adapter.
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Create a new statement.
     *
     * @param AdapterInterface $adapter a database adapter.
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setDbAdapter($adapter);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDbAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDbAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Returns a {@link PlatformInterface} object that contains platform specific informaion.
     *
     * @return PlatformInterface object containing information for a platform.
     */
    public function getPlatform()
    {
        return $this->adapter->getDriver()->getPlatform();
    }
    
    /**
     * Returns a {@link ConnectionInterface} object to communicate with the database.
     *
     * @return ConnectionInterface object to communicate with the database.
     */
    public function getConnection()
    {
        return $this->adapter->getDriver()->getConnection();
    }
    
    /**
     * Prepares an SQL statement for execution and returns a statement object.
     *
     * @return StatementInterface a {@link StatementInterface} object.
     * @see ConnectionInterface::prepare($sql)
     * @link http://php.net/manual/en/pdo.prepare.php
     */
    public function prepare()
    {
        return $this->getConnection()->prepare($this->getSqlString());
    }
    
    /**
     * Tests whether this object is dirty.
     *
     * @return bool true if this object is dirty, false otherwise.
     */
    public function isDirty()
    {
        return ($this->state === self::IS_DIRTY);    
    }
    
    /**
     * Tests whether this object is clean.
     *
     * @return bool true if this object is clean, false otherwise.
     */
    public function isClean()
    {
        return ($this->state === self::IS_CLEAN);
    }

    /**
     * Add a new query part with the given name.
     *
     * @param string $name the name of the query part.
     * @param mixed $part the part to add.
     * @param bool $append (optional) if true will append the part, otherwise all existing parts are first removed.
     */
    protected function addQueryPart($name, $part, $append = true)
    {
        if (!$append) {
            $this->clearQueryPart($name);
        }

        if (isset($this->parts[$name])) {
            $queryParts = $this->parts[$name];
            if (is_array($queryParts)) {
                if (!is_array($part)) {
                    $queryParts[] = $part;
                } else {   
                    $queryParts = array_merge($queryParts, $part);
                }
            } else {
                $queryParts = $part;
            }
            
            $this->parts[$name] = $queryParts;
        } else {
            $this->parts[$name] = $part;
        }
        
        $this->setState(self::IS_DIRTY);
    }
    
    /**
     * Returns if present the query part with the give name, otherwise the default value is returned.
     *
     * @param string $name the name of the query part to return.
     * @param mixed $default the value to return if no part exists for the given name.
     * @return mixed the part associated with the given name, or the default value.
     */
    protected function getQueryPart($name, $default = array())
    {
        return (array_key_exists($name, $this->parts)) ? $this->parts[$name] : $default;
    }
    
    /**
     * Replaces all parts for the query part with the given name with the initial value.
     *
     * @param string $name the name of the query part to remove.
     * @param mixed $initial the value to reset the empty query part to.
     */
    protected function clearQueryPart($name, $initial = null)
    {
        $this->parts[$name] = $initial;
        $this->setState(self::IS_DIRTY);
    }
    
    /**
     * Set the state of this object.
     *
     * @param int $state the state.
     */
    protected function setState($state)
    {
        $this->state = $state;
    }
    
    /**
     * Returns the state of this object.
     *
     * @return int the state.
     */
    protected function getState()
    {
        return $this->state;
    }
    
    /**
     * Returns a limit clause for the given arguments.
     *
     * @param Limit $limit (optional) the number of results to retrieve.
     * @param Offset|null $offset (optional) the record to start retrieving results at.
     * @return string a LIMIT clause for this specific platform.
     * @see PlatformInterface::getLimitClause($limit, $offset)
     */
    protected function limitResults(Limit $limit = null, Offset $offset = null)
    {
        $limit  = ($limit !== null) ? $limit->getLimit() : null;
        $offset = ($offset !== null) ? $offset->getOffset() : null;
    
        return $this->getPlatform()->getLimitClause($limit, $offset);
    }
    
    /**
     * Returns the generated SQL string.
     *
     * @return string a SQL statement.
     */
    abstract public function getSqlString();
    
    /**
     * Returns a string representation of this select statement.
     * 
     * @return string a string representation of this select statement.
     */
    public function __toString()
    {
        return $this->getSqlString();
    }
    
    /**
     * Create a copy of the query builder in it's current state.
     *
     * When cloning an object it's pointers will be copied. This means that any changes made to a cloned object will
     * still be reflected on the original object. So by cloning all objects we ensure that a deep copy is performed.
     *
     * @link http://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        foreach ($this->parts as $name => $part) {
            if (is_array($part)) {
                foreach ($this->parts[$name] as $index => $expression) {
                    if (is_object($expression)) {
                        $this->parts[$name][$index] = clone $expression;
                    }
                }
            } else if (is_object($part)) {
                $this->parts[$name] = clone $part;
            }
        }
    }
}
