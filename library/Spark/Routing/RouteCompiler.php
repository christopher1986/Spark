<?php

namespace Spark\Routing;

use Spark\Routing\Ast\NodeInterface;
use Spark\Routing\Ast\Node\Optional;
use Spark\Routing\Ast\Node\Parameter;
use Spark\Routing\Ast\Node\Text;
use Spark\Routing\Ast\Visitor\HierarchicalVisitorInterface;

/**
 * A hierarchial visitor which compiles a route by visting all nodes within a tree. 
 * A RouteCompiler compiles the given route into a regular expression which can be 
 * obtained through the {@link RouteCompiler::getRegex()} method. 
 *
 * A compiler can be reused by calling the {@link RouteCompiler::reset()} method 
 * which will reset all parameters of the compiler to their original values.
 *
 * @author Chris Harris 
 * @version 0.0.1
 */
class RouteCompiler implements HierarchicalVisitorInterface
{
    /**
     * A collection of parameter constraints.
     *
     * @var array
     */
    private $constraints = array();
    
    /**
     * A collection of parameters.
     *
     * @var array
     */
    private $params = array();
    
    /**
     * A regular expression.
     *
     * @var string
     */
    private $regex = '';

    /**
     * Construct the RouteCompiler.
     *
     * @param array|Traversable $constraints (optional) the constraints for the route to compile.
     */
    public function __construct($constraints = array())
    {
        $this->setConstraints($constraints);
    }
    

    /**
     * Set a collection of constraints which will be used to compile the route. Only string values 
     * from the given collection will be retained.
     *
     * The collection should consist of key-value pairs where each key maps to a route parameter
     * and the value is a valid regular expression used to match characters with. The following
     * code is a valid collection of constraints:
     *
     * array(
     *     'name' => '\w+',
     *     'age'  => '[0-9]+',
     * );
     *
     * @param array|Traversable $constraints the constraints for the route to compile.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function setConstraints($constraints)
    {
        if (!is_array($constraints) && !($constraints instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s(): expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($constraints) ? get_class($constraints) : gettype($constraints))
            ));
        }
        
        if ($constraints instanceof \Traversable) {
            $constraints = iterator_to_array($constraints);
        }
        
        $this->constraints = array_filter($constraints, 'is_string');
    }
    
    /**
     * Returns true if the given segment has a constraint.
     *
     * @param mixed $name the name of the constraint.
     * @return bool true if a constraint exists for the given segment, false otherwise.
     */
    public function hasConstraint($name)
    {
        return (isset($this->constraints[$name]));
    }
    
    /**
     * Returns if present the constraint for the given name, otherwise the default value is returned.
     *
     * @param mixed $name the name of the constraint.
     * @return string the constraint for the given name, or the default value.
     */
    public function getConstraint($name, $default = '[^/]+')
    {
        return ($this->hasConstraint($name)) ? $this->constraints[$name] : (string) $default;
    }
    
    /**
     * Returns a regular expression which was compiled by visting all nodes.
     *
     * @return string a regular expression.
     */
    public function getRegex()
    {
        return $this->regex;
    }
    
    /**
     * Returns all parameters that were found by visting all nodes.
     *
     * @return array a collection of parameters.
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Resets the compiler to it's original state.
     *
     * @return void
     */
    public function reset()
    {
        $this->constraints = array();
        $this->params      = array();
        $this->regex       = '';
    }
        
    /**
     * {@inheritDoc}
     */
    public function visitEnter(NodeInterface $node)
    {
        if ($node instanceof Optional && !$node->isLeaf()) {
            $this->regex .= '(?:';
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function visit(NodeInterface $node)
    {
        if ($node instanceof Parameter) {
            $this->params[] = $node->getName();
            $this->regex .= sprintf('(?P<%1$s>%2$s)', $node->getName(), $this->getConstraint($node->getName()));
        } else if ($node instanceof Text) {
            $this->regex .= $node->getText();
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function visitLeave(NodeInterface $node)
    {
        if ($node instanceof Optional && !$node->isLeaf()) {
            $this->regex .= ')?';
        }
        
        return true;
    }
}
