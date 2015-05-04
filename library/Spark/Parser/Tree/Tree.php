<?php

namespace Spark\Parser\Tree;

use Spark\Parser\Tree\Node\NodeInterface;
use Spark\Parser\Tree\Iterator\TreeIterator;
use Spark\Parser\Tree\Iterator\DepthFirstIterator;

/**
 * An undirected graph, also known as a tree. A tree connects two vertices by exactly one path.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Tree implements TreeInterface
{
    /**
     * @var NodeInterface The root node.
     */
    private $rootNode;
    
    /**
     * @var TreeIterator An iterator object.
     */
    private $iterator;
    
    /**
     * Create a tree.
     *
     * @param NodeInterface|null $node the root node.
     */
    public function __construct(NodeInterface $node = null)
    {
        $this->setRootNode($node);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setTreeIterator(TreeIterator $iterator)
    {
        $this->iterator = $iterator;
        $this->iterator->setTree($this);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTreeIterator()
    {
        if ($this->iterator === null) {
            $this->iterator = new DepthFirstIterator($this);
        }
        return $this->iterator;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setRootNode(NodeInterface $node = null)
    {
        $this->rootNode = $node;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->getRootNode() === null);
    }
}
