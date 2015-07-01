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
class Limit
{
    /**
     * The amount of results to retrieve.
     * 
     * @var int|null
     */
    private $limit = null;
    
    /**
     * Create a new limit.
     *
     * @param int $limit the amount of results to retrieve.
     */
    public function __construct($limit)
    {
        $this->setLimit($limit);
    }
    
    /**
     * Set the amount of results to retrieve.
     *
     * @param int|null $limit the amount of result results to retrieve, or null to remove any previously limit.
     * @throws InvalidArgumentException if the given argument is not a number or null literal.
     * @throws LogicException if the given limit is a negative number.
     */
    private function setLimit($limit = null)
    {
        if (!(is_numeric($limit) || $limit === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument or null literal; received "%s"',
                __METHOD__,
                (is_object($limit)) ? get_class($limit) : gettype($limit)
            ));
        } else if (is_numeric($limit) && $limit < 0) {
            throw new \LogicException(sprintf(
                '%s: expects an absolute value; recived "%s"',
                __METHOD__,
                $limit
            ));
        }
        
        $this->limit = (is_numeric($limit)) ? (int) $limit : null;
    }
    
    /**
     * Returns the amount of results to retrieve.
     *
     * @return int|null the amount of results to retrieve, or null if there is no limit.
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $limit = '';
        if (is_numeric($this->limit)) {
            $limit = sprintf('LIMIT %d', (int) $limit);
        }
        
        return $limit;
    }
}
