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

namespace Framework\Parser\Tree\Node;

use ArrayIterator;
use Framework\Collection\ArrayList;

/**
 * Implements all the functionality of a node so that this object can be used in a tree.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Node implements NodeInterface
{
    /**
     * @var string the name of this node.
     */
    private $name;

    /**
     * @var NodeInterface|null A parent node.
     */
    private $parent;
    
    /**
     * @var ListInterface a collection of child nodes.
     */
    private $children;
    
    /**
     * @var int The depth of this node within the tree.
     */
    private $depth = -1;
    
    /**
     * Create a node.
     *
     * @param string $name the name of this node.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function isLeaf()
    {
        return ($this->getChildCount() == 0);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setParent(NodeInterface $node = null)
    {
        // if necessary invalidate node.
        if ($this->parent !== $node) {
            $this->invalidate();
        }
        
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
     * Returns an iterator to iterate this node.
     *
     * @return Iterator an iterator object.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getChildren()->toArray());
    }
    
    /**
     * {@inheritDoc}
     */
    public function addChildren($nodes)
    {
        if (!is_array($nodes) && !($nodes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($nodes) ? get_class($nodes) : gettype($nodes))
            ));
        }
        
        $childAdded = false;
        foreach ($nodes as $node) {
            if ($this->addChild($node)) {
                $childAdded = true;
            }
        }
        
        return $childAdded;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addChild(NodeInterface $node)
    {
        // don't allow a loop construction.
        if ($this->isParent($node)) {
            throw new \LogicException(sprintf(
                '%s: loop constructions are not permitted; received "%s" which is already a parent node',
                __METHOD__,
                (is_object($node) ? get_class($node) : gettype($node))
            ));
        }
    
        $childAdded = $this->getChildren()->add($node);
        if ($childAdded && $node instanceof Node) {
            $node->setParent($this);
        } 
        
        return $childAdded;
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasChild(NodeInterface $node)
    {
        return $this->getChildren()->contains($node);
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeChild(NodeInterface $node)
    {
        $removedChild = $this->getChildren()->remove($node);
        if ($removedChild !== null) {
            $removedChild->setParent(null);
        }
        
        return $removedChild;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clearChildren()
    {   
        $children = $this->getChildren();
        foreach ($children as $child) {
            $child->setParent(null);
        }
        $children->clear();
    }
    
    /**
     * Returns a collection containing child nodes.
     *
     * @return ListInterface a collection of child nodes.
     */
    protected function getChildren()
    {
        if ($this->children === null) {
            $this->children = new ArrayList();
        }
        
        return $this->children;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getChildCount()
    {
        return (count($this->getChildren()));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDepth()
    {
        if ($this->depth < 0) {
            $depth = 0;
            if (($node = $this->getParent()) !== null) {
                while ($node !== null) {
                    $depth++;
                    $node = $node->getParent();
                }
            }
            
            $this->depth = $depth;
        }
        
        return $this->depth;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
	    
	    $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Invalidate this node and all of it's children.
     */
    public function invalidate()
    {
        $this->depth = -1;
        
        // invalidate if possible children.
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Node) {
                $child->invalidate();
            }
        }
    }
    
    /**
     * Tests whether the given node is a parent node of this node.
     *
     * @return bool true if the given node is a parent of this node, false otherwise.
     */
    private function isParent(NodeInterface $node)
    {
        $nodes = array();
        if (($node = $this->getParent()) !== null) {
            // populate array with parent nodes.
            while ($node !== null) {
                $nodes[] = $node;
                $node = $node->getParent();
            }
        }

        return (in_array($node, $nodes, true));
    }
}
