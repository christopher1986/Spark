<?php

namespace Main\Debug 
{
    use SplFileObject;
    use Spark\Common\Annotation\AnnotationLexer as AlsoSomethingElse,
        Spark\Scanner\PhpScanner as SomethingElse;
    use Spark\Io\StringReader;

    /**
     * A class used for testing purposes of custom reflection methods.
     * A second line
     * 
     * @author Chris Harris
     * @Spark\Annotation\Test(name = 'chris', age = 28)
     */
    class Test
    {
        /**
         * The name.
         *
         * @var string
         */
        private $name;

        /**
         * Construct object.
         */
        public function __construct()
        {
            new StringReader('test');
        }

        /**
         * Set the name.
         *
         * @param string $name the name.
         */
        public function setName($name)
        {
            $this->name = name;
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
}

namespace Main\Debug\Something
{
    include_once __DIR__ . '/Test/Test.php';

    use Spark\Io\StringReader as SomeOtherReader;

    class TestTwo
    {
        /**
         * Construct object.
         */
        public function __construct()
        {
            echo doSomething();
        }
    
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
            $this->name = name;
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
}
