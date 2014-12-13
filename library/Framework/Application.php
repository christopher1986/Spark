<?php

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
