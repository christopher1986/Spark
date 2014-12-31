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
 * Reads text from a reader and stores the current character, word or line from that reader for prolonged use.
 * 
 * Since the CachedReader stores characters for prolonged use it's efficient when a character, word or line should be retrievable 
 * without constantly setting a mark on the reader and resetting that mark again when your done. So compared to the other readers 
 * it's memory efficient when you need to retrieve the same character, word or line numerous times.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class CachedReader implements 
CacheableReader,
CacheableWordReader,
CacheableLineReader,
ReaderAware
{
    /**
     * A reader to read a character stream.
     *
     * @var Reader
     */
    private $reader;

    /**
     * An associative array that contains stored characters.
     *
     * @var array
     */
    private $cache = array();
    
    /**
     * Create a CachedReader that decorates a reader.
     *
     * @param Reader A reader to read characters from.
     */
    public function __construct(Reader $reader)
    {
        $this->setReader($reader);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
        $this->invalidate();
    }
    
    /**
     * Returns the reader used to read a character stream.
     *
     * @return Reader the reader from which characters are read.
     */
    public function getReader()
    {
        return $this->reader;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getChar()
    {
        if (isset($this->cache['char'])) {
            return $this->cache['char'];
        }
        
        $this->cache['char'] = null;
        if ($reader = $this->getReader()) {
            $this->cache['char'] = $reader->readCharAt($reader->getPosition());
        }
        return $this->cache['char'];
    }
    
    /**
     * {@inheritDoc}
     */
    public function getWord()
    {
        if (isset($this->cache['word'])) {
            return $this->cache['word'];
        }
        
        $this->cache['word'] = null;
        if ($reader = $this->getReader()) {
            $reader->mark();
            $this->cache['word'] = $reader->readWord();
            $reader->reset();
        }
        return $this->cache['word'];       
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLine()
    {
        if (isset($this->cache['line'])) {
            return $this->cache['line'];
        }
        
        $this->cache['line'] = null;
        if ($reader = $this->getReader()) {
            $reader->mark();
            $this->cache['line'] = $reader->readLine();
            $reader->reset();
        }
        return $this->cache['line'];
    }    
    
    /**
     * {@inheritDoc}
     */
    public function consumeChar()
    {
        if ($reader = $this->getReader()) {
            $this->invalidate();
            return ($reader->readChar() !== null);
        }
        return false; 
    }
    
    /**
     * {@inheritDoc}
     */
    public function consumeWord()
    {
        if ($reader = $this->getReader()) {
            $this->invalidate();
            return ($reader->readWord() !== null);
        }
        return false; 
    }
    
    /**
     * {@inheritDoc}
     */
    public function consumeLine()
    {
        if ($reader = $this->getReader()) {
            $this->invalidate();
            return ($reader->readLine() !== null);
        }
        return false; 
    }
    
    /**
     * Skip the given number of characters.
     *
     * @param int $amount the number of characters to skip.
     */
    public function skip($amount = 1)
    {
        if ($reader = $this->getReader()) {
            $this->invalidate();
            $reader->skip($amount);
        }
    }
    
    /**
     * Peeks ahead by the given number of characters and returns all characters found.
     *
     * @param string $amount the number of characters to peek forward.
     * @return string|null the character(s) found during the peek, or null if no characters are left.
     */
    public function peek($amount = 1)
    {
        $chars = null;
        if ($reader = $this->getReader()) {
            $chars = $reader->peek($amount);
        }
        return $chars;
    }
    
    /**
     * Invalidates all the characters currenty stored by the reader.
     *
     * @return void
     */
    protected function invalidate()
    {
        $this->cache = array();
    }
}
