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

namespace Spark\Db;

use Spark\Db\Adapter\AdapterAwareInterface;
use Spark\Db\Adapter\AdapterCapableInterface;

/**
 *
 *
 * @author Chris Harris
 * @version 0.0.1
 * @since 0.0.1
 */
interface QueryBuilderInterface extends AdapterAwareInterface, AdapterCapableInterface
{
    /**
     * Create a Select statement for the given columns.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder()
     *                            ->select('u.name')
     *                            ->from('users', 'u')
     *                            ->where('u.is_active = :active')
     *                            ->andWhere('u.name = :name');
     *
     *    $stmt = $queryBuilder->prepare();
     *    $stmt->bindParam(':active', 1, StatementInterface::PARAM_INT);
     *    $stmt->bindParam(':name', 'John', StatementInterface::PARAM_STR);
     *    
     *    $results = $stmt->fetchAll();
     *
     * </code>
     *
     * @param string|array|Traversable $select either a string for a single column or a collection for multiple columns.
     * @return Select a Select object to retrieve records from the underlying database.
     * @see Select
     */
    public function select($select);
    
    /**
     * Create a Select statement using a (raw) vendor-specific expression.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder()
     *                            ->rawSelect('CASE WHEN u.id IN (1, 3, 5, 7) THEN t.date_created ELSE t.last_modified END AS date');
     *
     *    $stmt = $queryBuilder->prepare();
     *    $column = $stmt->fetchColumn();
     * </code>
     *
     * @param string $expression a raw expression.
     * @return Select a Select object to retrieve records from the underlying database.
     * @see Select
     */
    public function rawselect($select);
    
    /**
     * Create an Insert statement for the given table name.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->insert('users')
     *                            ->values(array('name' => :name, 'username' => :username));
     *
     *    $stmt = $queryBuilder->prepare();
     *    $stmt->execute(array(':name' => 'John', ':username' => 'john'));
     * </code>
     *
     * @param string $table the name of a table into which values will be inserted.
     * @return Insert an Insert object to insert records into the underlying database.
     * @see Insert
     */
    public function insert($table);
    
    /**
     * Create a Delete statement for the given table name.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder()
     *                            ->delete('users', 'u')
     *                            ->where('u.name = :name');
     *
     *    $stmt = $queryBuilder->prepare();
     *    $stmt->execute(array(':name' => 'john'));
     * </code>
     *
     * @param string $table the name of a table whose records will be deleted.
     * @param string $alias (optional) the alias for this table.
     * @return Delete a Delete object to delete records from the underlying database.
     * @see Delete
     */
    public function delete($table, $alias = '');
    
    /**
     * Create an Update statement for the given table name.
     *
     * <code>
     *    $adapter = new Adapter(array('driver' => 'wpdb'));
     *    $queryBuilder = $adapter->getQueryBuilder()
     *                            ->update('users', 'u')
     *                            ->set('name', ':name')
     *                            ->where('u.is_active = :active');
     *
     *    $stmt = $queryBuilder->prepare();
     *    $stmt->bindParam(':name', 'John', StatementInterface::PARAM_STR);
     *    $stmt->bindParam(':active', 1, StatementInterface::PARAM_INT);
     *    $stmt->execute();
     * </code>
     *
     * @param string $table the name of a table whose records will be updated.
     * @param string $alias (optional) the alias for this table.
     * @return Update an Update object to update records from the underlying database.
     * @see Update
     */ 
    public function update($table, $alias = '');
}
