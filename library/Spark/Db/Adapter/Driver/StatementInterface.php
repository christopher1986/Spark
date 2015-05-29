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

namespace Spark\Db\Adapter\Driver;

interface StatementInterface
{  
    /**
     * Indicates a string value.
     *
     * @var int
     */
    const PARAM_STR = 0x01;
    
    /**
     * Indicates an integer value.
     *
     * @var int
     */
    const PARAM_INT = 0x02;
    
    /**
     * Indicates a float value.
     *
     * @var int
     */
    const PARAM_FLOAT = 0x04;

    /**
     * Fetches results as associative array.
     *
     * @var string
     */
    const FETCH_ASSOC = 'ARRAY_A';
    
    /**
     * Fetches results as numeric array.
     *
     * @var string
     */
    const FETCH_NUM = 'ARRAY_N';
    
    /**
     * Fetches results as anonymous object. 
     *
     * @var string
     */
    const FETCH_OBJ = 'OBJECT';
    
    /**
     * Executes a prepared statement. If the prepared statement includes parameters, you must
     * either call the {@link ConnectionInterface::bindParam} method to bind values to parameters
     * or pass an array containing key-values pairs where the keys match the the parameter name.
     *
     * @param array|null $params a collection of values to bind with the parameters, or null.
     * @return bool true on success or false on failure.
     */
    public function execute(array $params = null);
    
    /**
     * Fetches a single row from the resultset.
     *
     * @param string $type (optional) determines how the row is returned.
     * @param int $rowOffset (optional) the desired row, defaults to 0.
     * @return mixed a single row for this statement.
     */
    public function fetch($type = self::FETCH_OBJ, $rowOffset = 0);
    
    /**
     * Fetches the entire resultset.
     *
     * @param string $type (optional) determines how the resultset is returned.
     * @return mixed the entire resultset for this statement.
     */
    public function fetchAll($type = self::FETCH_OBJ);
    
    /**
     * Fetches a single column from the resultset.
     *
     * @param string $type (optional) determines how the column is returned.
     * @param int $columnOffset (optional) the desired column, defaults to 0.
     * @return mixed a single column for this statement.
     */
    public function fetchColumn($columnOffset = 0);
    
    /**
     * Returns the number of rows affected by the last query.
     *
     * @return int the number of rows.
     */
    public function rowCount();
    
    /**
     * Set the value for a named parameter.
     *
     * @param string $name the parameter name.
     * @param mixed $value the value to set.
     * @param type explicit data type for the parameter.
     * @link http://php.net/manual/en/pdostatement.bindvalue.php PDOStatement::bindValue
     */
    public function bindParam($name, $value, $type = self::PARAM_STR);
    
    /**
     * Unbind the value associated with the named parameter.
     *
     * @param string $name the parameter whose value to unbind.
     */
    public function unbindParam($name);
    
    /**
     * Tests whether a parameter exists for the given name.
     *
     * @param string $name the parameter name.
     * @return bool true if a parameter exists for the given name, false otherwise.
     */
    public function hasParam($name);
    
    /**
     * Remove all parameters. A statement will have no parameters anymore after this method returns.
     *
     * @return void
     */
    public function clearParams();
}
