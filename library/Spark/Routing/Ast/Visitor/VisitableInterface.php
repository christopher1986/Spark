<?php

namespace Spark\Routing\Ast\Visitor;

/**
 * The VisitableInterface allows an visitor to perform one or more operations
 * on the visitee. 
 *
 * @author Chris Harris
 * @version 0.0.1
 * @link http://java.dzone.com/articles/design-patterns-visitor
 */
interface VisitableInterface
{
    /**
     * Allows a visitor to perform operation(s) on this object.
     *
     * @param VisitorInterface $visitor a visitor object.
     * @return bool true to indicate that the visit was successful, otherwise false should be returned.
     */
    public function accept(VisitorInterface $visitor);
}
