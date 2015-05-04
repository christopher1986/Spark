<?php

namespace Spark\Routing\Ast\Iterator;

use Iterator;
use SplStack;

/**
 * The PostorderIterator is used to traverse a tree using the depth-first search (DFS) algorithm.
 * Within the DFS algorithm there are three types of depth-first traversal: pre-order, in-order 
 * and post-order. This iterator implements the latter type. 
 *
 * Be aware that this iterator does not check for concurrent modifications within the tree. 
 * So changes made to the tree such as moving a node or removing it while using this iterator
 * may result in non-deterministic behavior.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class PostorderIterator implements Iterator
{
    /**
     * A stack containing child nodes to visit.
     *
     * @var SplStack
     */
    public $stack;

    /**
     * The current node that is being visited.
     *
     * @var NodeInterface
     */
    public $current;

    /**
     * The tree containing nodes.
     *
     * @var NodeInterface
     */
    private $tree;

    /**
     * Construct a tree iterator which implements a depth-first search (DFS) algorithm.
     *
     * @param NodeInterface $node the root node.
     */
    public function __construct(NodeInterface $node)
    {
        $this->tree = $node;
        $this->rewind();
    }
    
    /**
     * Returns the current node.
     *
     * @return NodeInterface the current node.
     */    
    public function current()
    {
        return $this->current;
    }
    
    /**
     * Returns the index of the current node. 
     *
     * The index returned by this method is the position of the child within it's parent. 
     * So the left-most child node would return 0, it's sibling would return 1 and so forth. 
     * So the same key might be returned for two completely different nodes since they are 
     * located at different positions within the tree, and thus have different parent nodes. 
     *
     * @return int the position of the current node within it's parent.
     */
    public function key()
    {
        return $this->stack->top()->key();
    }
    
    /**
     * Move forward to the next node.
     *
     * @return void
     */    
    public function next()
    {
        $node = null;
        if (!$this->stack->isEmpty()) {
            // move to sibling node. 
            $it = $this->stack->top();
            $it->next();                    

            if ($it->valid()) {
                // get lest-most child of sibling branche.
                $node = $this->depthfirst($it->current());
            } else {
                // remove top iterator.
                $this->stack->pop();
                // move to parent node.
                $node = $this->current()->getParent();
            }
        }
        
        $this->current = $node;
    }

    /**
     * Rewind iterator to the first node.
     *
     * @return void
     */
    public function rewind()
    {
        // new empty stack.
        $this->stack = new SplStack();        
        // traverse tree for it's lest-most child node.
        $this->current = $this->depthfirst($this->tree);        
    }
    
    /**
     * Returns the left-most child node of the branch to which the given node belongs.
     *
     * @param NodeInterface $current the starting node from which to traverse the lest-most child node.
     * @return NodeInterface the left-most child of the tree, or if the node that was given is a leaf
     *                       node that node is returned unchanged.
     */
    private function depthfirst(NodeInterface $node)
    {
        // traverse tree for it's lest-most child node.
        while (!$node->isLeaf()) {
            // children of this node.
            $it = $node->getIterator();
            // push iterator onto stack.
            $this->stack->push($it);
            // lest-most child node.
            $node = $it->current();
        }
        return $node;
    }
    
    /**
     * Checks if the current position contains a node.
     *
     * @return bool true if the current position is valid, false otherwise.
     */
    public function valid()
    {
        return ($this->current !== null);
    }
}
