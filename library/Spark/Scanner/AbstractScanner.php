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

namespace Spark\Scanner;

/**
 * The AbstractScanner consists of methods that help to determine where a scanner is when processing 
 * a sequence of characters. 
 *
 * Within a scanner this is known as the scanners context. The scanner does not necessarily have to be 
 * in one single context at a time and the {@link AbstractScanner::hasContext($context)} method can be 
 * used to determine if the scanner currently resides in a specific context.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
abstract class AbstractScanner implements ScannerInterface
{
    /**
     * the context is the current position of the scanner within a sequence of characters. 
     *
     * @var int
     */ 
    protected $context = 0x00;
    
    /**
     * Add the given context to scanner.
     *
     * The scanner will be in the given context after this operation returns.
     * This can be tested for using the {@link AbstractScanner::hasContext($context)} method.
     *
     * @parem int context the context the place the scanner in.
     */
    public function addContext($context)
    {
        $this->context |= $context;
    }
    
    /**
     * Replaces the current context of the scanner.
     * 
     * If the context is omitted the context of the will be reset to it's initial value.
     *
     * @param int a new context that will replace the current context. 
     */
    public function setContext($context = 0x00)
    {
        $this->context = $context;
    }
    
    /**
     * Determine whether the scanner is currently in the given context.
     *
     * @param int context the context whose presence will be tested.
     * @return bool true if the scanner is currenty positioned in the given context, false otherwise.
     */
    public function hasContext($context)
    {
        return (($this->context & $context) === $context);
    }
    
    /**
     * Removes if present the given context.
     *
     * The scanner will no longer be in the given context after this operation returns.
     * This can be tested using the {@link AbstractScanner::hasContext($context)} method.
     *
     * @param int the context which the scanner will no longer be in.
     */
    public function removeContext($context)
    {
        $this->context &= ~$context;
    }
    
    /**
     * Resets the context to it's original value.
     */
    public function resetContext()
    {
        $this->context = 0x00;
    }
    
    /**
     * Returns true if the scanner has no context.
     *
     * @return bool true if no context is set, false otherwise.
     */
    public function isContextFree()
    {
        return ($this->context === 0x00);
    }
}
