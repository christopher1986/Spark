<?php

namespace Spark\Routing\Ast\Visitor;

use Spark\Routing\Ast\NodeInterface;

/**
 * The HierarchicalVisitorInterface is similar to the VisitorInterface but allows you to track
 * when a visitor is entering and leaving a composite node.
 *
 * @author Chris Harris
 * @version 0.0.1
 * @link http://c2.com/cgi/wiki?HierarchicalVisitorPattern
 */
interface HierarchicalVisitorInterface extends VisitorInterface
{
    /**
     * Applies one or more operations before children of the node are visited.
     *
     * @param NodeInterface $node the node on which to apply the operations.
     * @return bool false indicates that the composite traversal should be immediately stopped, otherwise true.
     */
    public function visitEnter(NodeInterface $node);
    
    /**
     * Applies one or more operations after children of the node have been visited.
     *
     * @param NodeInterface $node the node on which to apply the operations.
     * @return bool false indicates that the composite traversal should be immediately stopped, otherwise true.
     */
    public function visitLeave(NodeInterface $node);
}
