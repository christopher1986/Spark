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

abstract class AbstractStatement implements AdapterAwareInterface, AdapterCapableInterface
{
    /**
     * A database adapter.
     *
     * @var AdapterInterface
     */
    private $adapter;

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
     * Returns a list of columns using the platform column separator between each column.
     *
     * @param array $columns a collection of column names.
     * @return string a list of columns separated using a platform specific separator.
     * @see PlatformInterface::getColumnSeparator()
     */
    protected function listColumns(array $columns)
    {
        $separator = sprintf('%s ', $this->getPlatform()->getColumnSeparator());
        return implode($separator, $columns);
    }
    
    /**
     * Returns a limit clause for the given arguments.
     *
     * @param int $limit the number of results to retrieve.
     * @param int|null $offset (optional) the record to start retrieving results at.
     * @return string a LIMIT clause for this specific platform.
     * @see PlatformInterface::getLimitClause($limit, $offset)
     */
    protected function limitResults($limit = null, $offset = null)
    {
        return $this->getPlatform()->getLimitClause($limit, $offset);
    }
    
    /**
     * Returns the generated SQL string.
     *
     * @return string a SQL statement.
     */
    abstract public function getSqlString();
}
