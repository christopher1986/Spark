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

use SplFileObject;

use Spark\Io\Exception\IOException;

/**
 * The FileOutputStream is thin wrapper around a PHP stream used to write data
 * to a file on the file system.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class FileOutputStream implements StreamInterface
{
    /**
     * The underlying temp stream.
     *
     * @var resource
     */
    private $stream = null;
    
    /**
     * Options with which the stream is instantiated.
     *
     * @var array
     */
    private $options = array(
        'maxmemory' => 2097152,
    );
    
    /**
     * Construct a new FileOutputStream.
     *
     * @param string|SplFileObject $file the file to which the data will be written.
     * @param array|Traversable $options (optional) options used by the stream.
     */
    public function __construct($file, $options = array())
    {
        $this->setFile($file);
        $this->setOptions($options);
        $this->createStream();
    }
    
    /**
     * Returns the file to which the data will be written.
     *
     * @return SplFileObject the file to which the data is written.
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Returns the options with which the stream was instantiated.
     *
     * @return array the options currently used by the stream.
     */
    public function getOptions()
    {
        return $this->options;
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
    {
        $this->ensureOpen();
        
        // write stream content to file.
        if (($file = $this->getFile()) !== null) {
            rewind($this->stream);
            $file->fwrite(stream_get_contents($this->stream));
        }
        
        // (re)create new stream.
        $this->createStream();
    }
    
    /** 
     * {@inheritDoc}
     */
    public function close()
    {
        $this->flush();
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
    
    /**
     * Creates a new PHP temp stream.
     *
     * @return void.
     */
    protected function createStream()
    {
        // close any previous stream.
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    
        $protocol = sprintf('php://temp/maxmemory:%d', (int) $this->getOption('maxmemory', 2097152));
        $this->stream = fopen($protocol, 'w');
    }
    
    /**
     * Set the file to which the data will be written.
     * 
     * A {@link SplFileObject} instance must be in write mode for this stream
     * to write it's data to the file. For more information on possible modes
     * see {@link http://php.net/manual/en/function.fopen.php file mode}.
     *
     * @param string|SplFileObject $file the file to which the data will be written.
     */
    protected function setFile($file)
    {        
        if (is_string($file)) {
            $file = new SplFileObject($file, 'w+');
        }
        
        if (!$file instanceof SplFileObject) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a path to a file or SplFileObject; received "%s"',
                __METHOD__,
                (is_object($file)) ? get_class($file) : gettype($file)
            ));
        }
    
        $this->file = $file;
    }
    
    /**
     * Set the options to be used by the stream.
     *
     * @param array|Traversable $options the options to use.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable.
     */
    protected function setOptions($options)
    {
        if ($options instanceof \Traversable) {
            $options = iterator_to_array($options);
        }
        
        if (!is_array($options)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable object; received "%s"',
                __METHOD__,
                (is_object($options)) ? get_class($options) : gettype($options)
            ));
        }
        
        $this->options = array_merge($this->options, $options);
    }
    
    /**
     * Returns the value for the specified option name.
     *
     * @param mixed $name the name of the option.
     * @param mixed $default the value to return if the specified name does not exist.
     * @return mixed the value associated with the specified option, or the default value.
     */
    private function getOption($name, $default = null)
    {
        return (array_key_exists($name, $this->options)) ? $this->options[$name] : $default;
    }
}
