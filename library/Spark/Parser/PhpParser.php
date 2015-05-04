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

namespace Spark\Parser;

use Spark\Io\Exception\AccessDeniedException;
use Spark\Io\Exception\FileNotFoundException;
use Spark\Scanner\PhpScanner;
use Spark\Parser\Tree\Node\PhpNode;

class PhpParser
{    
    /**
     * @var SplFileObject an object oriented interface to a file.
     */
    private $fileObject;

    /**
     * Create parser.
     *
     * @param string $filename path to a PHP file that will be parsed.
     */
    public function __construct($filename)
    {
        $this->createFileObject($filename);
    }

    /**
     * Parse the given content of a PHP file.
     *
     * @param string $content the file content that will be parsed.
     * @return Tree a 
     * @throws InvalidArgumentException if the given argument is not of type string.
     */
    public function parse()
    {        
        $fileObject = $this->getFileObject();
        
        $childNodes = array();
        
        $tokens = array_reverse($this->getTokens($this->getFileContent()));
        foreach ($tokens as $token) {
            switch ($token->identify()) {
                case PhpScanner::T_NAMESPACE:
                    
                    break;
                case PhpScanner::T_USE_STATEMENT:

                    break;
                case PhpScanner::T_AS_STATEMENT:
                
                    break;
                case PhpScanner::T_INCLUDE_STATEMENT:
                
                    break;
                case PhpScanner::T_CLASS_NAME:
                
                    break;
            }
        }
    }
    
    /**
     * Returns an collection of tokens that are obtained through lexical analysis of the file content.
     *
     * @return array an array containing zero or more tokens.
     */
    private function getTokens($content)
    {
        $scanner = new PhpScanner($content);
        return $scanner->scan();
    }
    
    /**
     * Creates a SplFileObject object for the given file.
     *
     * @param string $filename the file that will be parsed.
     * @throws InvalidArgumentException if the given argument is not of type string.
     * @throws FileNotFoundException if given path point to a non-existing file.
     */
    private function createFileObject($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($filename) ? get_class($filename) : gettype($filename))
            ));
        } else if (!file_exists($filename)) {
            throw new FileNotFoundException($filename, 'file does not exist, or symbolic link is pointing non-existing file.');
        }
        
        $this->fileObject = new \SplFileObject($filename);
    }
    
    /**
     * Returns an object oriented interface to the file that will be parsed.
     *
     * @return SplFileObject a file object for the file that will be parsed.
     */
    private function getFileObject()
    {
        return $this->fileObject;
    }

    /**
     * Returns the content of the file in which the class has been defined.
     *
     * Unlike functions such as {@link file_get_contents} that reads an entire file into a string
     * this method will read the file line-by-line. Although reading a file line-by-line is slightly
     * slower it will most definitely use less memory in doing so.
     *
     * @param int $lineNumber the maximum number of lines to read.
     * @return string the content of the file, or an empty string if there is nothing to read.
     */
    private function getFileContent($lineNumber = -1)
    {
        $fileObject = $this->getFileObject();
        if (!$fileObject->isReadable()) {
            throw new AccessDeniedException($filename, 'unable to read file, make sure the file has read permissions.');
        }
            
        $lineCount = 0;
        $content = '';
        while (!$fileObject->eof()) {
            // reached the maximum number of lines to read.
            if ($lineNumber >= 0 && $lineCount++ == $lineNumber) {
                break;
            } 
            $content .= $fileObject->fgets();
        }
        
        return $content;
    }
}
