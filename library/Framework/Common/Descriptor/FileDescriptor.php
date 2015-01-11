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

namespace Framework\Common\Descriptor;

use SplFileObject;

use Framework\Io\Exception\AccessDeniedException;
use Framework\Io\Exception\FileNotFoundException;
use Framework\Scanner\PhpScanner;
use Framework\Util\Arrays;

/**
 * 
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
class FileDescriptor
{    
    /**
     * A default namespace to use if one is not provided.
     *
     * @var string
     */
    const DEFAULT_NAMESPACE = 'global';

    /**
     * An array containing information about namespaces.
     *
     * @var array
     */
    private $namespaces = array();

    /**
     * An object from which to read the contents of a file.
     *
     * @var SplFileObject
     */
    private $fileObject;

    /**
     * A flag to indicate if the namespace has been introspected.
     *
     * @var bool
     */
    private $hasIntrospected = false;

    /**
     * Create a FileDescriptor.
     *
     * @param string $filename the absolute path to a file.
     */
    public function __construct($filename)
    {    
        $this->createFileObject($filename);
        $this->introspect();
    }
    
    /**
     * Returns a collection of class names for the given namespace.
     *
     * @param string $namespace the namespace for which to return class names.
     * @return array a numeric array of class names.
     */
    public function getClasses($namespace = self::DEFAULT_NAMESPACE)
    {
        $namespaceKey = Arrays::normalizeKey($namespace);
        
        $classes = array();
        if (isset($this->namespaces[$namespaceKey]['classes'])) {
            $classes = $this->namespaces[$namespaceKey]['classes'];
        }
        return $classes;
    }
    
    /**
     * Returns a collection of use statements for the given namespace.
     *
     * A single use statement is defined as an associative array containing the use statement and
     * a possible alias. An example given is below:
     *  
     * array(
     *     array(
     *         'use'       => 'SomeNamespace\Foo',
     *         'as'        => NULL
     *     ),
     *     array(
     *         'use'       => 'SomeNamespace\Baz',
     *         'as'        => 'FooBar'
     *     ),
     * )
     *
     * @param string $namespace the namespace for which to return use statements.
     * @return array a multidimensional array of use statements.
     */
    public function getUses($namespace = self::DEFAULT_NAMESPACE)
    {
        $namespaceKey = Arrays::normalizeKey($namespace);

        $uses = array();
        if (isset($this->namespaces[$namespaceKey]['uses'])) {
            $uses = $this->namespaces[$namespaceKey]['uses'];
        }
        return $uses;
    }

    /**
     * Introspects the given file.
     *
     * @return void
     */
    private function introspect()
    {
        if ($this->hasIntrospected) {
            return;
        }
        
        // set namespace to 'global'.
        $namespaceKey = Arrays::normalizeKey(self::DEFAULT_NAMESPACE);
        
        $scanner = new PhpScanner($this->getFileContent());
        $tokens = $scanner->scan();

        foreach ($tokens as $token) {            
            switch ($token->identify()) {
                case PhpScanner::T_NAMESPACE:
                    // update key for new namespace.
                    $namespaceKey = Arrays::normalizeKey($token->getValue());
                    break;
                case PhpScanner::T_USE_STATEMENT:
                    $this->namespaces[$namespaceKey]['uses'][] = array('use' => $token->getValue(), 'as' => null);
                    break;
                case PhpScanner::T_AS_STATEMENT:
                    // move to last element.
                    end($this->namespaces[$namespaceKey]['uses']);
                    // get index of last element.
                    $index = key($this->namespaces[$namespaceKey]['uses']);
                    // add alias to use statement.
                    $this->namespaces[$namespaceKey]['uses'][$index]['as'] = $token->getValue();
                    break;
                case PhpScanner::T_CLASS_NAME:
                    $this->namespaces[$namespaceKey]['classes'][] = $token->getValue();
                    break;
            }
        }
        
        $this->hasIntrospected = true;
    }
    
    /**
     * Creates an object oriented interface for the given file.
     *
     * @param string $filename the file that will be parsed.
     * @throws InvalidArgumentException if the given argument is not of type string.
     * @throws FileNotFoundException if given path point to a non-existing file.
     * @link http://php.net/manual/en/class.splfileobject.php
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
            throw new FileNotFoundException($filename, 'file does not exist, or symbolic link is pointing to non-existing file.');
        }
        
        $this->fileObject = new SplFileObject($filename);
    }
    
    /**
     * Returns an object oriented interface for the file to describe.
     *
     * @return SplFileObject an object oriented interface for the file.
     * @link http://php.net/manual/en/class.splfileobject.php
     */
    private function getFileObject()
    {
        return $this->fileObject;
    }
    
    /**
     * Returns the content of the file.
     *
     * Unlike functions such as {@link file_get_contents} that reads an entire file into a string
     * this method will read the file line-by-line. Although reading a file line-by-line is slightly
     * slower it will most definitely use less memory in doing so.
     *
     * @param int $lineNumber the maximum number of lines to read.
     * @return string the content of the file, or an empty string if there is nothing to read.
     * @throws AccessDeniedException if the file to describe has no read permissions.
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
