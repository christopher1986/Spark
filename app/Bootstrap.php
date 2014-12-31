<?php

namespace Main;

use Main\Debug\Test;

use Framework\Application;
use Framework\Loader\AutoloaderFactory;
use Framework\Net\Uri;
use Framework\Common\Reflection\ClassReflection;

use Framework\Parser\Tree\Tree;
use Framework\Parser\Tree\Node\PhpNode;
use Framework\Parser\Tree\Iterator\BreadthFirstIterator;

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
 
    /*
    protected function _initTree()
    {        
        // nr. 2
        $includeNode = new PhpNode('INCLUDE', 'CHILD OF ROOT NODE: foo.php');

        // nr. 3
        $useNode = new PhpNode('USE', 'CHILD OF ROOT NODE: Framework\Util\Strings');
        // nr. 5
        $useNode->addChild(new PhpNode('USE', 'CHILD OF USE NODE: Framework\Util\Arrays'));
        // nr. 6
        $useNode->addChild(new PhpNode('USE', 'CHILD OF USE NODE: Framework\Collection\Set'));
        
        // nr. 4
        $classNode = new PhpNode('CLASS', 'CHILD OF ROOT NODE: FooClass');
        
        // nr. 1
        $rootNode = new PhpNode('FILE', 'ROOT NODE');
        $rootNode->addChild($includeNode); 
        $rootNode->addChild($useNode);
        $rootNode->addChild($classNode);
        
        $tree = new Tree($rootNode);
        $tree->setTreeIterator(new BreadthFirstIterator());
        
        $itr = $tree->getTreeIterator();

        foreach($itr as $node) {
            echo $node->getValue();
            echo '<br />'; 
        }       
    } 
    */
 
    protected function _initReflection()
    {
        $testObject = new Test();
        
        $reflection = new ClassReflection($testObject);
        $annotations = $reflection->getAnnotations();
    }
 
    protected function _initUri()
    {
        //$uri = new Uri('http://www.google.nl');
    }
}
