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

namespace Spark\Io\Stream;

use Spark\Io\Exception\IOException;

/**
 * The PrintStream decorates a {@link StreamInterface} instance and provides additional
 * methods which can be used to write data to the decorated stream.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class PrintStream implements StreamInterface
{
    /**
     * A stream to which the bytes will be written.
     *
     * @var StreamInterface
     */
    private $stream = null;

    /**
     * Construct a new PrintStream.
     *
     * @param StreamInterface $stream a stream to which the bytes will be written.
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }
    
    /**
     * {@inheritDoc}
     */
    public function write($str)
    {
        $this->ensureOpen();
        $this->stream->write($str);
    }
    
    /** 
     * Write the specified string accompanied with a line terminator to the stream.
     *
     * @param string $str (optional) the string which will be written to the stream.
     * @link http://php.net/manual/en/reserved.constants.php#constant.php-eol
     */
    public function writeln($str = '')
    {
        $this->ensureOpen();
        $this->stream->write($str);
        $this->stream->write(PHP_EOL);
    }
    
    /**
     * Write a formatted string to the stream.
     *
     * @param string $str a format string composed of zero or more conversion specifications.
     * @param mixed[] $args arguments to replace the conversion specifications with.
     * @link http://php.net/manual/en/function.sprintf.php
     */
    public function writef($str, $args = null)
    {
        $args = (is_array($args)) ? $args : array_slice(func_get_args(), 1);
        array_unshift($args, $str);
        
        $this->ensureOpen();
        $this->stream->write(call_user_func_array('sprintf', $args));
    }
    
    /** 
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->stream->flush();
    }
    
    /** 
     * {@inheritDoc}
     */
    public function close()
    {
        $this->flush();
        
        $this->stream->close();
        $this->stream = null;
    }
    
    /**
     * Ensures that the underlying stream is still open.
     *
     * @throws IOException if the stream has been closed.
     */
    protected function ensureOpen()
    {
        if ($this->stream === null) {
            throw new IOException('Stream closed');
        }
    }
}
