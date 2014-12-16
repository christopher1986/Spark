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

use Framework\Io\Exception\IOException;

/**
 * A StreamReader is capable of reading characters from a stream or file.
 * 
 * The StreamReader will not close existing streams. This means that resources 
 * created outside the StreamReader will still need to be closed once they are 
 * no longer necessary, for example using PHP's {@link fclose($handle)} function.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class StreamReader extends StringReader
{
    /**
     * Creates a new StreamReader, given the stream to read.
     *
     * @param resource|string $stream the stream or file to read.
     */
    public function __construct($stream)
    {
        if ($content = $this->getStreamContent($stream)) {
            $this->setContent($content);
        }
    }

    /**
     * Returns the content from the given stream or file.
     *
     * @param resource|string $stream the stream or file to read.
     * @return string|null the content from the stream or file, or null
     *                     if the content could not be read.
     */
    protected function getStreamContent($stream)
    {    
        if (!is_string($stream) && !is_resource($stream)) {
            throw new IOException(sprintf(
                '%s: expects a resource or string as argument; received "%s"',
                __METHOD__,
                (is_object($stream) ? get_class($stream) : gettype($stream))
            ));
        }
        
        if (is_string($stream)) {
            // read file contents into string.
            $content = file_get_contents($stream);
        } else {
            // read resource into string.
            $content = stream_get_contents($stream);
        }
        
        return (is_string($content)) ? $content : null;
    }
}
