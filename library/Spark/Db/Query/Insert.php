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

/**
 *
 *
 * @author Chris Harris
 * @version 0.0.1
 * @since 0.0.2
 */
class Insert extends AbstractSql
{
    /**
     * The parts that form the insert statement.
     *
     * @var array
     */
    protected $parts = array(
        'table'    => null,
        'values'   => array(),
    );
    
    /**
     * Specify which table into which new rows will be inserted.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->insert('users');
     * </code>
     *
     * @param string $table the table name.
     * @param string $alias (optional) the alias for this table.
     * @return Insert allows a fluent interface to be created.
     * @throws InvalidArgumentException if the first argument is not a 'string' type.
     */
    public function into($table)
    {
        if (!is_string($table)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($table)) ? get_class($table) : gettype($table)
            ));
        }
        
        $this->addQueryPart('table', $table, false);
        return $this;
    }
    
    /**
     * Add one or more columns and values to the statement. The specified collection should consist 
     * of key-value pairs where a key represents the column name and the value will be inserted into 
     * that column.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->insert('users')
     *                            ->values(array('name' => ':name', 'username' => ':username', 'password' => ':password'));
     * </code>
     *
     * @param array $values a collection consisting of key-value pairs.
     * @return Insert allows a fluent interface to be created.
     */
    public function values(array $values)
    {
        $this->parts['values'] = array_merge($this->parts['values'], $values);
        return $this;
    }
    
    /**
     * Add a column and value to the statement.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->insert('users')
     *                            ->value('name', ':name')
     *                            ->value('username', ':username')
     *                            ->value('password', ':password');
     * </code>
     *
     * @param string $column the name of a column.
     * @param mixed $value the value.
     * @return Insert allows a fluent interface to be created.
     */
    public function value($column, $value)
    {
        $this->parts['values'][$column] = $value;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSqlString()
    {
        if ($this->isClean()) {
            return $this->query;
        }
        
        $query  = sprintf('INSERT INTO %s', $this->parts['table']);
        $query .= sprintf('(%s) ', implode(', ', array_keys($this->parts['values'])));
        $query .= sprintf('VALUES (%s)', implode(', ', $this->parts['values']));
        
        // update state of object.
        $this->setState(self::IS_CLEAN);
        // store generated SQL statement.
        $this->query = rtrim($query);

        return $this->query;
    }
}
