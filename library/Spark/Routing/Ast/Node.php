<?php

namespace Spark\Routing\Ast;

use ArrayIterator;

use Spark\Routing\Ast\Visitor\VisitorInterface;

/**
 * An abstract node which provides a skeletal implements of the NodeInterface to minimize the effort
 * required to create a node.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
abstract class Node implements NodeInterface
{
    /**
     * The parent node.
     *
     * @var NodeInterface|null
     */
    private $parent;

    /**
     * The line number.
     *
     * @var int
     */
    private $lineNumber = -1;

    /**
     * A collection of child nodes.
     *
     * @var array
     */
    private $children = array();

    /**
     * Set the line number for this node.
     *
     * @param int $lineNumber the line number.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     * @throws LogicException if the given line number is a negative value.
     */
    public function setLineNumber($lineNumber) 
    {
        if (!is_numeric($lineNumber)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric value; received "%d"',
                __METHOD__,
                (is_object($lineNumber) ? get_class($lineNumber) : gettype($lineNumber))
            ));
        } else if ($lineNumber < 0) {
            throw new \LogicException(sprintf(
                '%s: line number must be 0 or greater; received "%d"',
                __METHOD__,
                $lineNumber
            ));   
        }
        
        $this->lineNumber = (int) $lineNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }
    
    /**
     * Set the parent of this node.
     * 
     * @param NodeInterface|null $node a parent node, or if null to remove any parent node.
     */
    public function setParent(NodeInterface $node = null)
    {
        $this->parent = $node;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function isLeaf()
    {
        return ($this->count() == 0);
    }
    
    /**
     * Set a collection of child nodes. Only elements within the given collection that implement the 
     * NodeInterface are retained by this node.
     *
     * @param array|Traversable a collection of nodes.
     * @throws \InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    public function setChildren($children)
    {
        if (!is_array($children) && !($children instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable object as argument; received "%s"',
                __METHOD__,
                (is_object($children) ? get_class($children) : gettype($children))
            ));
        }
        
        if ($children instanceof \Traversable) {
            $children = iterator_to_array($children);
        }
        
        // remove element which are not nodes.
        $children = array_filter($children, function($child) {
            return ($child instanceof NodeInterface);
        });
        // set the parent of all nodes.
        $success = array_walk($children, function($child) {
            $child->setParent($this);
        });

        $this->children = $children;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        // if necessary lazy initialize array.
        if (!is_array($this->children)) {
            $this->children = array();
        }
    
        return $this->children;
    }
    
    /**
     * Returns an iterator to traverse the children in this node in proper sequence.
     *
     * @return Iterator an iterator to traverse the children in this node.
     * @link http://php.net/manual/en/class.iteratoraggregate.php the IteratorAggregate interface
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getChildren());
    }
    
    /**
     * Returns the number of child nodes contained by this node.
     *
     * @return int the number of child nodes.
     */
    public function count()
    {
        return count($this->getChildren());
    }
    
    /**
     * {@inheritDoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->visit($this);
    }
}
