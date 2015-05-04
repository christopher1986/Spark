<?php

namespace Spark\Routing\Ast\Visitor;

use Spark\Routing\Ast\NodeInterface;

/**
 * The VisitorInterface allows operations to be performed across a set of nodes. 
 *
 * This interface defines methods which provide additional functionality to one or more Node classes
 * without actually changing their inner workings. Although not evident at first sight this 
 * implementation does have one great advantage. The visitor helps to prevent a node class from 
 * being bloated with operations that are only practical to a limited number of objects.
 *
 * @author Chris Harris
 * @version 0.0.1
 * @link http://java.dzone.com/articles/design-patterns-visitor
 */
interface VisitorInterface
{    
    /**
     * Applies on or more operations on a node that is being visited.
     *
     * @param NodeInterface $node the node on which to apply the operations.
     * @return bool true to indicate that the visit was successful, otherwise false should be returned.
     */
    public function visit(NodeInterface $node);
}
