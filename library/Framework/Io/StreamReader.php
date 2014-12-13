<?php

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
        if ($content = $this->setStreamContent($stream)) {
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
