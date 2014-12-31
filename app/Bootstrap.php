<?php

namespace Main;

use Main\Debug\Foo;

use Framework\Application;
use Framework\Loader\AutoloaderFactory;
use Framework\Net\Uri;
use Framework\Common\Descriptor\ClassDescriptor;

require_once dirname(__DIR__) . '/library/Framework/Application.php';

/**
 * A bootstrap class which initializes the application.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Bootstrap extends Application
{
    /** 
     * Initialize autoloaders and register the 'Main' namespace.
     *
     * @return void
     */
    protected function _initAutoloaderConfig()
    {        
        AutoloaderFactory::factory(array(
            'Framework\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Main' => __DIR__,
                ),
            ),
        ));
    }
 
    protected function _initReflection()
    {
        $fooObject = new Foo();
        
        $classDescriptor = new ClassDescriptor($fooObject);
        $annotations = $classDescriptor->getAnnotations();
    }
 
    protected function _initUri()
    {
        //$uri = new Uri('http://www.google.nl');
    }
}
