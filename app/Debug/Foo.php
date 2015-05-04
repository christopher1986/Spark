<?php

namespace Main\Debug; 

include_once(__DIR__ . '/Test.php');

use SplFileObject;
use Spark\Common\Annotation\AnnotationLexer as AlsoSomethingElse,
    Spark\Scanner\PhpScanner as SomethingElse;
use Spark\Io\StringReader;

/**
 * A class used for testing purposes of custom reflection methods.
 * A second line
 * 
 * @author Chris Harris
 * @Spark\Annotation\Test(name = 'chris\"s dog ran away', numbers = {28, 10, 4})
 */
class Foo
{
    /**
     * The name.
     *
     * @var string
     */
    private $name;

    /**
     * Set the name.
     *
     * @param string $name the name.
     */
    public function setName($name)
    {
        $this->name = $name;
    } 
    
    /**
     * Returns the name.
     *
     * @return string the name.
     */
    public function getName()
    {
        return $this->name;
    }
}

