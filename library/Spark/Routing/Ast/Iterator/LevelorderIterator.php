<?php

namespace Spark\Routing\Ast\Iterator;

use Iterator;
use SplQueue;

/**
 * The LevelorderIterator is used to traverse a general tree using the breadth-first search (BFS)
 * algorithm. 
 *
 * Be aware that this iterator does not check for concurrent modifications within the tree. 
 * So changes made to the tree such as moving a node or removing it while using this iterator
 * may result in non-deterministic behavior.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class LevelorderIterator implements Iterator
{
    /**
     * A queue containing child nodes to visit.
     *
     * @var SplQueue
     */
    public $queue;

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
        return $this->queue->bottom()->key();
    }
    
    /**
     * Move forward to the next node.
     *
     * @return void
     */    
    public function next()
    {               
        $this->current = $this->breadthfirst();
    }

    /**
     * Rewind iterator to the first node.
     *
     * @return void
     */
    public function rewind()
    {
        // new empty queue.
        $this->queue = new SplQueue();        
        // set tree as current node.
        $this->current = $this->tree;
        // add iterator with child nodes to queue.
        if (!$this->current->isLeaf()) {
            $this->queue->enqueue($this->current->getIterator());        
        }
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
    
    /**
     * Returns the next node by recursively calling the {@link LevelorderIterator::nextNode()} method.
     *
     * @return NodeInterface|null the next node, or null if no nodes are left.
     */
    private function breadthfirst()
    {
        $node = null;
        if (!$this->queue->isEmpty()) {
            // nodes of the currently highest level.
            $it = $this->queue->bottom();
            
            if ($it->valid()) {
                // current node of this level.
                $node = $it->current();
                // add iterator with child nodes to queue.
                if (!$node->isLeaf()) {
                    $this->queue->enqueue($node->getIterator());
                }
                
                // move to sibling for next iteration.
                $it->next();
            } else {
                // remove highest level.
                $this->queue->dequeue();          
                // returns the first node of the next level.
                return $this->breadthfirst();
            }
        }
        
        return $node; 
    }    
}
