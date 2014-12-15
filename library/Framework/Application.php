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

namespace Framework;

use Framework\Loader\AutoloaderFactory;

use ReflectionClass;
use ReflectionMethod;

/**
 * The application class is responsible for initializing the application.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Application
{
    /**
     * Create a new Application.
     *
     * @return void
     */
    public function __construct()
    {        
        $methods = $this->getClassMethods($this);
        foreach ($methods as $method) {
            // call parameterless methods which are prefixed with '_init'.
            if ($method->getNumberOfParameters() == 0 && strpos($method->getName(), '_init') === 0) {
                $method->setAccessible(true);
                $method->invoke($this);
            }
        }
    }
    
    /** 
     * Initialize autoloaders and register the 'Framework' namespace.
     *
     * @return void
     */
    private function _initAutoloaderConfig()
    {
        require_once __DIR__ . '/Loader/AutoloaderFactory.php';
        
        AutoloaderFactory::factory(array(
            'Framework\Loader\StandardAutoloader' => array(
                'autoregister_framework' => true
            ),
        ));
    }
    
    /**
     * Returns an array containing {@link Reflectionmethod}s for the given class.
     * 
     * The {@link ReflectionMethod}s are stored in such a way that methods of parent classes are stored 
     * before methods of their child class. If methods of the child class should come first use the 
     * {@link ReflectionClass::getMethods($filter)} method instead.
     *
     * @param string|object $class the class to return methods for.
     * @return array returns an array containing zero or more reflection methods.
     * @link http://php.net/manual/en/reflectionclass.getparentclass.php
     * @link http://php.net/manual/en/reflectionclass.getmethods.php
     */
    private function getClassMethods($class) 
    {
        $class = new ReflectionClass($class);
        $classMethods = $class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE);
        
        $methods = (is_array($classMethods)) ? $classMethods : array();
        while ($parentClass = $class->getParentClass()) {
            $parentMethods = $parentClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE);
            if (is_array($parentMethods)) {
                $methods = array_merge($parentMethods, $methods);
            }
            
            // search all parent classes.
            $class = $parentClass;
        }
        return $methods;
    }
}
