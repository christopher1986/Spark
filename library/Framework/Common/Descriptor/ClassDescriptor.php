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

use Framework\Cache\Storage\ArrayStorage;
use Framework\Common\Annotation\AnnotationLoader;
use Framework\Common\Annotation\AnnotationScanner;

/**
 * 
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
class ClassDescriptor
{
    /**
     *  The class to describe.
     * 
     * @var ReflectionClass
     */
    private $reflectedClass;

    /**
     * 
     */
    private $annotations;

    /**
     * A collection of annotations for this class.
     *
     * @var array
     */
    private $annotationLoader;
    
    /**
     * Create a ClassDescriptor.
     *
     * @param string|ReflectionClass $class the class to describe.
     */
    public function __construct($class)
    {    
        $this->setClass($class);
        
        /*
        $fileDescriptor = new FileDescriptor($this->getFileName());
        echo '<pre>';
        var_dump($fileDescriptor->getUses($this->getNamespaceName()));
        echo '</pre>';
        */
    }
    
    public function getAnnotationLoader()
    {
        if ($this->annotationLoader === null) {
            $this->annotationLoader = new AnnotationLoader($this->getClass());
        }
        
        return $this->annotationLoader;
    }
    
    public function getAnnotations()
    {
        
        if ($this->annotations === null) {
            $scanner = new AnnotationScanner($this->getClass()->getDocComment());
            $tokens = $scanner->scan();
        }
        
        return $this->annotations;
    }
    
    /**
     * Returns the name of the namespace.
     *
     * @return string the name of the namespace. 
     */
    public function getNamespaceName()
    {
        return $this->getClass()->getNamespaceName();
    }
    
    /**
     * Return the filename of the file in which the class has been defined.
     *
     * @return string|null the filename of the file, or if the class is defined in the PHP core
     *                     or in a PHP extension, null is returned.
     */
    public function getFileName()
    {
        $filename = $this->getClass()->getFileName();
        return ($filename === false) ? null : $filename;
    }
    
    /**
     * Returns the short name of the class.
     *
     * @return string the shortname of the class.
     */
    public function getShortName()
    {
        return $this->getClass()->getShortName();
    }
    
    /**
     * Set class to describe.
     *
     * @param mixed a class to reflect or a ReflectionClass.
     */
    private function setClass($class)
    {        
        if (is_string($class) || (is_object($class) && !($class instanceof \Reflector))) {
            $class = new ReflectionClass($class);
        }

        if (!($class instanceof ReflectionClass)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a class name or ReflectionClass as argument; received "%s"',
                __METHOD__,
                (is_object($class) ? get_class($class) : gettype($class))
            ));
        }
        
        $this->reflectedClass = $class;
    }
    
    /**
     * Returns the class to introspect.
     *
     * @return ReflectionClass a reflected class.
     */
    private function getClass()
    {
        return $this->reflectedClass;
    }
    
    /**
     * Returns a caching object.
     *
     * @return ArrayStorage a storage object.
     */
    private function getCache()
    {
        if (self::$cache === null) {
            self::$cache = new ArrayStorage();
        }
        return self::$cache;
    }
    
    /**
     * Returns a hash id for this class.
     *
     * @return string a
     */
    private function getClassUID()
    {
        $reflClass = $this->getClass();
        
        $filename = $reflClass->getFileName();
        if (false === $filename) {
            $filename = '';
        }
        
        return md5(sprintf('%s::%s', $filename, $reflClass->getShortName()));
    }
}
