<?php

namespace Spark\Routing\Ast\Node;

use Spark\Routing\Ast\Node;
use Spark\Routing\Ast\VisitorInterface;

class Parameter extends Node
{
    /**
     * The parameter name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Construct a new ParameterNode.
     *
     * @param string $name the parameter name.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Set the name of this parameter.
     *
     * @param string $name the parameter name.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function setName($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($strname)
            ));
	    }
    
        $this->name = ltrim($name, ':');
    }
    
    /**
     * Returns the name of this parameter.
     *
     * @return string the name of this parameter.
     */
    public function getName()
    {
        return $this->name;
    }
}
