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

namespace Framework\Io;

/**
 * An abstract class for reading characters from a string.
 * 
 * @author Chris Harris
 * @version 1.0.0
 */
abstract class Reader
{
    /**
     * Reader is unmarked.
     *
     * @var int
     */
    const UNMARKED = -1;

    /**
     * Read the given amount of the characters from the reader.
     * 
     * @param int $amount the number of characters to read.
     * @return string|null a string containing the given amount of characters.
     */ 
    public function readChar($amount = 1)
    {
        return $this->read(0, $amount);
    }
    
    /**
     * Reads the given amount of the characters from the reader starting 
     * from the given offset. 
     *
     * @param int $offset the offset at which to start storing characters.
     * @param int $length the maximum number of characters to read.
     * @return string a string consisting of the characters read.
     * @throws \InvalidArgumentException if the given arguments are not integer types.
     */
    public abstract function read($offset, $length);    
    
    /**
     * Mark the current position of the reader. Calling reset() will
     * reset the reader postion to the marked position.
     *
     */
    public abstract function mark();
    
    /**
     * Reset the reader to the most recent mark.
     *
     * @throws \LogicException if no has mark has been set.
     */
    public abstract function reset();
    
    /**
     * Returns the position of the reader within the stream.
     *
     * @return int the position within the stream.
     */
    public abstract function getPosition();
    
    /**
     * Skip the given number of characters.
     *
     * @param int $amount the number of characters to skip.
     */
    public abstract function skip($amount = 1);
    
    /**
     * Peeks ahead by the given number of characters and returns all characters found.
     *
     * @param string $amount the number of characters to peek forward.
     * @return string|null the character(s) found during the peek, or null if no characters are left.
     */
    public abstract function peek($amount = 1);
}
