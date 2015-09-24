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
 * The OutputStream is thin wrapper around the PHP output stream.
 * 
 * This write-only stream allows you to write bytes to the output
 * buffer mechanism in the same way as print and echo.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class OutputStream implements StreamInterface
{
    /**
     * A PHP output stream.
     *
     * @var resource
     */
    private $stream = null;
    
    /**
     * Construct a new OutputStream.
     */
    public function __construct()
    {
        $this->stream = fopen('php://output', 'w');
    }

    /** 
     * {@inheritDoc}
     *
     * @throws IOException if the stream has been closed.
     */
    public function write($str)
    {    
        $this->ensureOpen();
        fputs($this->stream, $str);
    }
    
    /** 
     * {@inheritDoc}
     *
     * This stream does not use a buffer, instead the specified bytes are directly written to the
     * PHP output stream which means that the {@link StreamInterface::flush()} method is not used.
     */
    public function flush()
    {}
    
    /** 
     * {@inheritDoc}
     */
    public function close()
    {
        if (fclose($this->stream)) {
            $this->stream = null;
        }
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
