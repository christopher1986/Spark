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
class Offset
{
    /**
     * The offset at which to start retrieving results.
     * 
     * @var int|null
     */
    private $offset;
    
    /**
     * Create a new offset.
     *
     * @param int $offset the offset which to start retrieving results.
     */
    public function __construct($offset)
    {
        $this->setOffset($offset);
    }
    
    /**
     * Set the offset at which to start retrieving results.
     *
     * @param int $offset the offset which to start retrieving results.
     * @throws InvalidArgumentException if the given argument is not a number.
     * @throws LogicException if the given offset is a negative number.
     */
    private function setOffset($offset)
    {
        if (!(is_numeric($offset) || $offset === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument or null literal; received "%s"',
                __METHOD__,
                (is_object($offset)) ? get_class($offset) : gettype($offset)
            ));
        } else if (is_numeric($offset) && $offset < 0) {
            throw new \LogicException(sprintf(
                '%s: expects an absolute value; recived "%s"',
                __METHOD__,
                $offset
            ));
        }
        
        $this->offset = (int) $offset;
    }
    
    /**
     * Returns the offset at which to start retrieving results.
     *
     * @return int|null the offset at which to start retrieving results, or null if there is no offset.
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $offset = '';
        if (is_numeric($this->offset)) {
            $offset = sprintf('OFFSET %d', (int) $this->offset);
        }
        
        return $offset;
    }
}
