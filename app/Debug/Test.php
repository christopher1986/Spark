<?php

namespace Main\Debug 
{
    use SplFileObject;
    use Framework\Common\Annotation\AnnotationScanner as AlsoSomethingElse,
        Framework\Scanner\PhpScanner as SomethingElse;
    use Framework\Io\StringReader;

    /**
     * A class used for testing purposes of custom reflection methods.
     * A second line
     * 
     * @author Chris Harris
     * @Framework\Annotation\Test(name = 'chris', age = 28)
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
    use Framework\Io\StringReader as SomeOtherReader;

    class TestTwo
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
