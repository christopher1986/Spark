<?php

namespace Framework\Parser\Tree;

use Framework\Parser\Tree\Node\NodeInterface;
use Framework\Parser\Tree\Iterator\TreeIterator;

/**
 * A data structure that simulates a hierarchical tree structure, with a root value and zero or more subtrees consisting of child nodes.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface TreeInterface
{
    /**
     * Set a iterator object to iterate over all nodes within the tree.
     *
     * @param TreeIterator $iterator an iterator object.
     */
    public function setTreeIterator(TreeIterator $iterator);
    
    /**
     * Returns an iterator object to iterate over all nodes within the tree.
     *
     * @return Iterator an iterator object.
     */
    public function getTreeIterator();
    
    /**
     * Set the given node as root node.
     *
     * @param NodeInterface|null $node the node to set as root node, or null to remove any previous root node.
     */
    public function setRootNode(NodeInterface $node = null);
    
    /**
     * Returns if present the root node.
     *
     * @return NodeInterface|null the root node, or null if this tree has no nodes.
     */
    public function getRootNode();
    
    /**
     * Returns true if this tree contains no nodes.
     *
     * @return bool true is this tree contains no nodes, false otherwise.
     */
    public function isEmpty();
}
