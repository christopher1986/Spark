<?php

namespace Main;

use Main\Debug\Foo;
use Main\Debug\Test;

use Spark\Application;
use Spark\Cache\Configuration\FileConfiguration;
use Spark\Cache\Storage\FileStorage;
use Spark\Collection\ArrayList;
use Spark\Common\Descriptor\ClassDescriptor;
use Spark\Loader\AutoloaderFactory;
use Spark\Net\Uri;
use Spark\Http\Request;

use Spark\Routing\Router;
use Spark\Routing\RouteParser;
use Spark\Routing\RouteCompiler;
use Spark\Routing\Route\Route;
use Spark\Routing\Route\RouteCollection;

require_once dirname(__DIR__) . '/library/Spark/Application.php';

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
            'Spark\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Main' => __DIR__,
                ),
            ),
        ));
    }
    
    /*
    protected function _initNodes()
    {   
        $createRoute = function($length = 10, $parts = 2) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz:';
            $charactersLength = strlen($characters);
            
            $paths = array();
            for ($i = 0; $i < $parts; $i++) {
                $segment = '';
                for ($j = 0; $j < $length; $j++) {
                    $segment .= $characters[rand(0, $charactersLength - 1)];
                }
                $paths[] = rtrim($segment, ':');
            }
            

            return implode('/', $paths);
        };
    
        $start = microtime(true);
        for ($i = 0; $i < 5000; $i++) {
            $parser->parse($createRoute(5,3));
        }          
        var_dump(microtime(true) - $start);            
    } 
    */
    
    protected function _initRoutes()
    {   
        // the HTTP request.
        $request = new Request();

        $collection = new RouteCollection();
        $collection->add('category', new Route('/category/:category_name/feed/:feed/?', array(), array('category_name' => '.+?', 'feed' => 'feed|rdf|rss|rss2|atom')));
        $collection->add('page', new Route('/:pagename', array(), array('pagename' => '.+')));

        

        $router = new Router();
        $router->addRoutes($collection);
        
        if ($routeMatch = $router->match($request)) {
            echo '<p><strong>route:</strong> ' . $routeMatch->getRouteName() . '</p>';
            echo '<p><strong>param:</strong> ' . var_export($routeMatch->getParams(), true) . '</p>';
        }
        
        exit; 
    }
    
    
    /**
    protected function _initCache()
    {
        $config = new FileConfiguration(array(
            'cache_dir' => __DIR__ . \DIRECTORY_SEPARATOR . 'cache',
            'file_permission' => 0644,
            'dir_permission' => 0755,
            'time_to_live' => 25,
        ));
        
        $storage = new FileStorage($config);
        $storage->add('leo', 'file caching werkt');

        $value = $storage->get('leo');
        echo $value;
    }
    */
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    protected function _initReflection()
    {       
        $fooObject = new Foo();
        
        $start = microtime(true);
        
        $classDescriptor = new ClassDescriptor($fooObject);
        //$loader = $classDescriptor->getAnnotationLoader();
        //$loader->getAnnotation('Test');
        
        $annotations = $classDescriptor->getAnnotations();
        
        $elapsed = microtime(true) - $start;
        
        echo $elapsed;
    }
    */
    
    protected function initUri()
    {
        //$uri = new Uri('http://www.google.nl');
    }
    
}
