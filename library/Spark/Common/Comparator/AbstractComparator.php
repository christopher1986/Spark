<?php
/**
 * Copyright (c) 2014, Chris Harris.
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
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Common\Comparator;

/**
 * This class provides a skeleton implementation of the Comparable interface, to minimize the effort required to implement this interface.
 *
 * In order to use this class a developer only needs to implements the abstract methods which define if a comparator can handle both values
 * and if so how both values should be ordered.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class AbstractComparator implements ComparatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare($firstValue, $secondValue)
    {
        $retval = 0;
        if ($this->accepts($firstValue, $secondValue)) {
            $retval = $this->internalCompare($firstValue, $secondValue);
        }
        return $retval;
    }
    
    /**
     * Returns true if this comparator is capable comparing both values, otherwise false should be returned allowing another comparator
     * a change to compare both values.
     * 
     * @param mixed $firstValue the first value to be compared.
     * @param mixed $secondValue the second value to be compared.
     * @return bool true if a comparator is capable of comparing both values, otherwise false.
     */
    public abstract function accepts($firstValue, $secondValue);
    
    /**
     * Compares both arguments for order. Returns a negative integer, zero or positive integer as the first argument is less than, 
     * equal to, or greater than the second. 
     *
     * @param mixed $firstValue the first value to be compared.
     * @param mixed $secondValue the second value to be compared.
     * @return int a negative, zero or positive integer as the first argument is less than, equal to or greater than the second.
     *              
     */
    protected abstract function internalCompare($firstValue, $secondValue);
}
