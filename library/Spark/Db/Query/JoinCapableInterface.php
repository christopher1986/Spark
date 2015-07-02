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
 * @since 0.0.1
 */
interface JoinCapableInterface
{
    /**
     * Creates and adds a join to the query. 
     *
     * Because a SQL join is equal to a inner join this method acts as an alias to 
     * the {@link JoinCapableInterface::innerJoin($fromAlias, $join, $alias, $condition)} method.
     *
     * <code>
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->select('t.name')
     *                            ->from('table', 't')
     *                            ->join('user', 'u', 't.user_id = u.id');
     * </code>
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function join($join, $alias, $condition);

    /**
     * Creates and adds a inner join to the query.
     *
     * <code>
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->select('t.name')
     *                            ->from('table', 't')
     *                            ->innerJoin('user', 'u', 't.user_id = u.id');
     * </code>
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function innerJoin($join, $alias, $condition);

    /**
     * Creates and adds a left join to the query.
     *
     * <code>
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->select('t.name')
     *                            ->from('table', 't')
     *                            ->leftJoin('user', 'u', 't.user_id = u.id');
     * </code>
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function leftJoin($join, $alias, $condition);
    
    /**
     * Creates and adds a right join to the query.
     *
     * <code>
     *    $queryBuilder = $adapter->getQueryBuilder();
     *                            ->select('t.name')
     *                            ->from('table', 't')
     *                            ->rightJoin('user', 'u', 't.user_id = u.id');
     * </code>
     *
     * @param string $join the table to join with.
     * @param string $alias the alias for the join table.
     * @param string $condition the condition for the join.
     */
    public function rightJoin($join, $alias, $condition);    
}
