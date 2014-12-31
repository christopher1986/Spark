<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */


namespace Framework\Parser\Tree\Iterator;

use Framework\Parser\Tree\TreeInterface;

/**
 * A depth-first iterator for an undirected graph, also known as a tree. To prevent any non-deterministic behavior the tree 
 * should not be modified during iteration.
 * 
 * This iterator will create a depth-first sorted list on which it can iterate. This means that any changes made to the tree 
 * during iteration will not be reflected by this iterator until it has been rewound again.
 *
 * @author Chris Harris 
 */
class DepthFirstIterator implements TreeIterator
{
    /**
     * @var array a collection of nodes.
     */
    private $nodes = array();
    
    /**
     * @var TreeInterface A tree over which to iterate.
     */
    private $tree;
    
    /**
     * Create the iterator.
     *
     * @param TreeInterface|null $tree the tree over which to iterate, or null.
     */
    public function __construct(TreeInterface $tree = null)
    {
        if ($tree !== null) {
            $this->setTree($tree);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function setTree(TreeInterface $tree)
    {
        $this->tree = $tree;
    }
    
    /**
     * Returns the tree on which to iterate.
     *
     * @return TreeInterface the tree on which to iterate.
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * {@inheritDoc}
     */
    public function setTreeIterator(TreeInterface $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Returns the current node.
     *
     * @return mixed the current node.
     */
    public function current()
    {
        return current($this->nodes);
    }
    
    /**
     * Returns the key for the current node.
     *
     * @return scalar the key of the current node.
     */
    public function key()
    {
        return key($this->nodes);
    }
    
    /**
     * Move forward to the next node.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->nodes)); 
    }
    
    /**
     * Rewind iterator to the first node.
     */
    public function rewind()
    {
        $tree = $this->getTree();
        if ($tree !== null) {
            $this->nodes = $this->buildList($tree);
        } else {
            $this->nodes = array();
        }
        
        $this->valid = (false !== reset($this->nodes));
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return bool true if the current position is valid, false otherwise.
     */
    public function valid()
    {
        return $this->valid;
    }
    
    /**
     * Build a depth first list by traversing all nodes in the given tree.
     *
     * @param TreeInterface a tree from which to build a list.
     * @return array a collection of nodes stored in  depth first order.
     */
    private function buildList(TreeInterface $tree)
    { 
        $dfsList = array();
        if (($rootNode = $tree->getRootNode()) !== null) {
            // add node to list.
            $dfsList[] = $rootNode;
            // push iterator onto stack.
            $stack = array($rootNode->getIterator());
            
            while (!empty($stack)) {
                // get top iterator from stack.
                $itr = end($stack);

                if ($itr->valid()) {
                    $node = $itr->current();
                    // add node to list.
                    $dfsList[] = $node;
                    // push iterator onto stack.
                    $stack[] = $node->getIterator();

                    $itr->next();
                } else {
                    // remove top iterator from stack.
                    array_pop($stack);
                }
            }
        }
                
        return $dfsList;
    }
}
