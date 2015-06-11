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

namespace Spark\Db\Adapter\Driver\Wpdb;

use Spark\Db\QueryBuilder;
use Spark\Db\Adapter\Driver\ConnectionInterface;

class Connection implements ConnectionInterface
{
    /**
     * A resource to connect with a database.
     *
     * @var wpdb
     */
    private $connection;
    
    /**
     * Create a new connection.
     *
     * @param wpdb $wpdb the resource to connect with the database.
     */
    public function __construct(\wpdb $wpdb)
    {
        $this->connection = $wpdb;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare($query)
    {
        return new Statement($this, $query);
    }
    
    /**
     * {@inheritDoc}
     */
    public function query($query)
    {
        $stmt = $this->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->connection;
    }
    
    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->connection->last_error;
    }
    
    /**
     * {@inheritDoc}
     */
    public function quote($input)
    {
        if (function_exists('esc_sql')) {
            return esc_sql($input);
        }
        return $this->connection->_escape($input);
    }
}
