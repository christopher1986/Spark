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

use Framework\Cache\ArrayStorage;
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
     * A cache to hold namespace descriptors.
     *
     * @var ArrayStorage
     */
    private static $cache;

    /**
     * A collection of annotations for this class.
     *
     * @var array
     */
    private $annotations = array();
    
    /**
     * Create a ClassDescriptor.
     *
     * @param string|ReflectionClass $class the class to describe.
     */
    public function __construct($class)
    {    
        $reflClass = ($class instanceof ReflectionClass) ? $class : new ReflectionClass($class);
        $this->setReflectedClass($reflClass);
    }
    
    public function getAnnotations()
    {
        $namespace = $this->getNamespace();
        
        echo '<pre>';
        var_dump($namespace->getUseStatements());
        echo '</pre>';
        
        if ($this->annotations === null) {
            $scanner = new AnnotationScanner($this->getDocComment());
            $this->annotations = $scanner->scan();
        }
        return $this->annotations;
    }
    
    /**
     * Returns a namespace descriptor for this class.
     *
     * @return NamespaceDescriptor an object that describes the namespace of this class.
     */
    public function getNamespace()
    {
        $cacheId = md5($this->getReflectedClass()->getFileName() . $this->getReflectedClass()->getName());
        
        $storage = $this->getCache(); 
        if (!$storage->has($cacheId)) {
            $storage->add($cacheId, new NamespaceDescriptor($this->getReflectedClass()));
        }

        return $storage->get($cacheId);
    }
    
    /**
     * Set a reflection object of the class that is introspected.
     *
     * @param ReflectionClass $reflClass a reflection class.
     */
    private function setReflectedClass(ReflectionClass $reflClass)
    {        
        if ($reflClass === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a Reader object as argument; received "null"',
                __METHOD__
            ));
        }
    
        $this->reflClass = $reflClass;
    }
    
    /**
     * Returns a reflection object of the class that is introspected.
     *
     * @return ReflectionClass a reflection class.
     */
    private function getReflectedClass()
    {
        return $this->reflClass;
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
}
