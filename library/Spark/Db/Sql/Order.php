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
 * The From class represents the table to retrieve results from.
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
class Order
{
    /**
     * Indicates that sorting is ascending.
     *
     * @var string
     */
    const SORT_ASC = 'ASC';
    
    /**
     * Indicates that sorting is descending.
     *
     * @var string
     */
    const SORT_DESC = 'DESC';

    /**
     * The column name of alias.
     *
     * @var string
     */
    private $column = '';
    
    /**
     * The sorting either ascending or descending.
     *
     * @var string
     */
    private $sort = '';
    
    /**
     * Create a new order.
     *
     * @param string $column the column or alias for which this order is created.
     * @param string $sort the sorting either ascending or descending.
     */
    public function __construct($column, $sort = self::SORT_ASC)
    {
        $this->setColumn($column);
        $this->setSort($sort);
    }
    
    /**
     * Set the column name or alias to which this order applies.
     *
     * @param string $column the column name of alias.
     */
    private function setColumn($column)
    {    
	    if (!is_string($column)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($column)) ? get_class($column) : gettype($column)
            ));
	    }
	    
	    $this->column = $column;
    }
    
    /**
     * Determine how the results shoud be sorted.
     *
     * @param string $sort the sorting to apply.
     * @throws InvalidArgumentException if the given sorting is not one of the Order constants.
     */
    public function setSort($sort)
    {
        $sort = strtoupper($sort);
        if (!in_array($sort, array(self::SORT_ASC, self::SORT_DESC))) {
            throw new \InvalidArgumentException(sprintf(
                '%s: unable to determine what sorting should be applied; received "%s"',
                __METHOD__,
                (is_object($sort)) ? get_class($sort) : $sort
            ));
        }
        
        $this->sort = $sort;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $order = $this->column; 
        if ($this->sort !== '') {
            $order = sprintf('%s %s', $order, $this->sort);
        }
        
        return $order;
    }
}
