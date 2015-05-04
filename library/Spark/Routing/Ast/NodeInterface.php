<?php

namespace Spark\Routing\Ast;

use Countable;
use IteratorAggregate;

use Spark\Routing\Ast\Visitor\VisitableInterface;

/**
 * Nodes are individual parts of a larger data structure, such as linked lists and tree data structures.
 * A node represents the information contained in a single structure. They may contain a value, represent
 * a condition, or possibly serve as another independant data structure. Nodes are hierarchical, meaning that
 * a node can have a (single) parent and zero or more child nodes. 
 *
 * The {@link http://en.wikipedia.org/wiki/Node_(computer_science) Node (computer science)} has more detailed
 * information about nodes and their practical usage.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
interface NodeInterface extends Countable, IteratorAggregate, VisitableInterface
{
    /**
     * Returns if present the line number. By default this method returns -1 if no 
     * line number was provided.
     *
     * @return int the line number, or -1 if no line number was provided.
     */
    public function getLineNumber();
    
    /**
     * Returns if present the parent node of this node.
     *
     * @return NodeInterface|null the parent of this node, or null.
     */
    public function getParent();
    
    /**
     * Returns true if node is a leaf.
     *
     * @return bool true if node is a leaf, false otherwise.
     */
    public function isLeaf();
    
    /**
     * Returns if present a collection of child nodes.
     *
     * @return array a collection of child nodes.
     */
    public function getChildren();
}
