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

namespace Spark\Parser\Tree\Node;

/**
 * Defines the requirements for an object that can be used as a node in a tree.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
interface NodeInterface extends \IteratorAggregate
{
    /**
     * Returns true if this node is a leaf of the tree.
     *
     * @return bool true if node is a leaf, false otherwise.
     */
    public function isLeaf();
    
    /**
     * Set the parent of this node.
     * 
     * @param NodeInterface|null $node a parent node, or if null to remove any parent node.
     */
    public function setParent(NodeInterface $node = null);
    
    /**
     * Returns if present the parent node of this node.
     *
     * @return NodeInterface|null the parent of this node, or null.
     */
    public function getParent();
    
    /**
     * Add a collection of children to this node.
     *
     * @param array|Traversable a collection of children to add.
     * @return bool true if at least one node was added, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type array or Traversable.
     * @throws Exception if an object within the collection is not a NodeInterface.
     * @throws LogicException if a node within the collection is a parent of this node.
     */
    public function addChildren($nodes);
    
    /**
     * Add the given node as a child of this node.
     *
     * @return NodeInterface the node to add as a child.
     * @return bool true if the node was added, false otherwise.
     * @throws LogicException if the given node is a parent of this node.
     */
    public function addChild(NodeInterface $node);
    
    /**
     * Determines whether the given node if a child of this node.
     *
     * @return bool true if the given node is a child of this node, false otherwise.
     */
    public function hasChild(NodeInterface $node);
    
    /**
     * Removes if present the given node from this node.
     *
     * @return NodeInterface|null the node that was removed, or null if the node was not found.
     */
    public function removeChild(NodeInterface $node);
    
    /**
     * Removes all children from this node. This node will have no children after this call returns.
     *
     * @return void
     */
    public function clearChildren();
    
    /**
     * Returns the number of children contained by this node.
     *
     * @return int the number of children.
     */
    public function getChildCount();
    
    /**
     * Returns the number of edges between this node and the tree's root node.
     *
     * @return int the depth of this node within the tree.
     */
    public function getDepth();
    
    /**
     * Returns the name of this node.
     *
     * @return string the name of this node.
     */
    public function getName();
    
    /**
     * Set the name of this node.
     *
     * @param string $name the name of this node.
     * @throws InvalidArgumentException if the given argument is not of type string.
     */
    public function setName($name);
}
