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

use ReflectionClass;
use SplFileObject;

use Framework\Io\Exception\AccessDeniedException;
use Framework\Scanner\PhpScanner;

/**
 * 
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
class NamespaceDescriptor
{
    /**
     *  A class whose namespace to describe.
     * 
     * @var ReflectionClass
     */
    private $reflClass;

    /**
     * The name of the namespace.
     *
     * @var string
     */
    private $namespace;

    /**
     * A collection of use statements.
     *
     * @var array
     */
    private $uses = array();
    
    /**
     * A collection of files to include.
     *
     * @var array
     */
    private $includes = array();

    /**
     * A collection of classes contained within the namespace.
     *
     * @var array
     */
    private $classNames = array();

    /**
     * Object to read the contents of the file containing the namespace.
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
     * Create a NamespaceDescriptor.
     *
     * @param string|ReflectionClass $class the class whose namespace to describe.
     */
    public function __construct($class)
    {    
        $reflClass = ($class instanceof ReflectionClass) ? $class : new ReflectionClass($class);
        $this->setReflectedClass($reflClass);
        $this->introspect();
    }
    
    /**
     * Returns the name of the namespace.
     *
     * @return string the name of the namespace. 
     */
    public function getNamespace()
    {
        if ($this->namespace === null) {
            $this->namespace = $this->getReflectedClass()->getNamespaceName();
        }
        return $this->namespace;
    }
    
    /**
     * Returns a collection of use statements.
     *
     * A single use statement is defined as an associative array containing the use statement and
     * a possible alias. An example given is below:
     *  
     * array(
     *     array(
     *         'use' => 'MyNamespace\Foo',
     *         'as'  => NULL
     *     ),
     *     array(
     *         'use' => 'MyNamespace\Baz',
     *         'as'  => 'FooBar'
     *     ),
     * )
     *
     * @return array a multidimensional array of use statements.
     */
    public function getUseStatements()
    {
        return $this->uses;
    }
    
    /**
     * Returns a collection of include statements.
     *
     * @return array a numeric array of include statements.
     */
    public function getIncludeStatements()
    {
        return $this->includes;
    }
    
    /**
     * Returns a collection of class names.
     *
     * @return array a numeric array of class names.
     */
    public function getClassNames()
    {
        return $this->classNames;
    }
    
    /**
     * Set a reflection object of the class whose namespace is introspected.
     *
     * @param ReflectionClass $reflClass a reflection class.
     */
    private function setReflectedClass(ReflectionClass $reflClass)
    {        
        if ($reflClass === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a ReflectionClass as argument; received "null"',
                __METHOD__
            ));
        }
    
        $this->reflClass = $reflClass;
    }
    
    /**
     * Returns a reclection object of the class whose namespace is introspected.
     *
     * @return ReflectionClass a reflection class.
     */
    private function getReflectedClass()
    {
        return $this->reflClass;
    }

    /**
     * Introspects the file in which the class has been defined.
     *
     * @return void
     */
    private function introspect()
    {
        if ($this->hasIntrospected) {
            return;
        }
        
        $scanner = new PhpScanner($this->getFileContent());
        $tokens = $scanner->scan();
        
        $withinNamespace = ($this->getReflectedClass()->inNamespace()) ? false : true;
        foreach ($tokens as $token) {
            if ($token->identify() == PhpScanner::T_NAMESPACE) {
                $withinNamespace = ($token->getValue() === $this->getNamespace());
            }
        
            if (!$withinNamespace) {
                continue;
            }
            
            switch ($token->identify()) {
                case PhpScanner::T_USE_STATEMENT:
                    $this->uses[] = array('use' => $token->getValue(), 'as' => null);
                    break;
                case PhpScanner::T_AS_STATEMENT:
                    // move to last use statement.
                    end($this->uses);
                    // get key for this statement.
                    $key = key($this->uses);
                    // store alias for last statement.
                    $this->uses[$key]['as'] = $token->getValue();
                case PhpScanner::T_INCLUDE_STATEMENT:
                    $this->includes[] = $token->getValue();
                    break;
                case PhpScanner::T_CLASS_NAME:
                    $this->classNames[] = $token->getValue();
                    break;
                
            }
        }
        
        $this->hasIntrospected = true;
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
    
    /**
     * Returns an object oriented interface for the file in which the class has been defined.
     *
     * @return SplFileObject an object oriented interface for a file.
     * @link http://php.net/manual/en/class.splfileobject.php
     */
    private function getFileObject()
    {
        if ($this->fileObject === null) {
            $this->fileObject = new SplFileObject($this->getReflectedClass()->getFileName());
        }
        return $this->fileObject;
    }
}
