<?php

namespace Spark\Routing\Ast\Iterator;

use Iterator;
use SplStack;

/**
 * The PreorderIterator is used to traverse a tree using the depth-first search (DFS) algorithm.
 * Within the DFS algorithm there are three types of depth-first traversal: pre-order, in-order 
 * and post-order. This iterator implements the former type. 
 *
 * Be aware that this iterator does not check for concurrent modifications within the tree. 
 * So changes made to the tree such as moving a node or removing it while using this iterator
 * may result in non-deterministic behavior.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class PreorderIterator implements Iterator
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
        if (!$this->current->isLeaf()) {
            // children of the current node.    
            $it = $this->current->getIterator();
            // push the iterator onto stack.
            $this->stack->push($it);
            // the current child of this iterator.
            $node = $it->current();
        } else if (!$this->stack->isEmpty()) {
            // try a sibling node.
            $it = $this->stack->top();
            $it->next();
            
            if ($it->valid()) {
                $node = $it->current();
            } else {
                // remove top iterator.
                $this->stack->pop();
                // move-up the tree and visit a new node.
                while (!$it->valid() && !$this->stack->isEmpty()) {
                    $it = $this->stack->pop();
                    $it->next();
                }
                
                $node = $it->current();

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
        // set tree as current node.
        $this->current = $this->tree;        
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
