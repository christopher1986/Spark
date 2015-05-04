<?php

namespace Spark\Routing\Ast\Node;

use Spark\Routing\Ast\Node;
use Spark\Routing\Ast\Visitor\VisitorInterface;
use Spark\Routing\Ast\Visitor\HierarchicalVisitorInterface;

class Optional extends Node
{
    /**
     * Construct a new OptionalNode.
     *
     * @param array|Traversable|null $children (optional) a collection of nodes.
     */
    public function __construct($children = null)
    {
        if ($children !== null) {
            $this->setChildren($children);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        if (($visitor instanceof HierarchicalVisitorInterface) && $visitor->visitEnter($this)) {            
            foreach ($this as $child) {
                $isValid = $child->accept($visitor);
                if (is_bool($isValid) && !$isValid) {
                    break;
                }
            }
            return $visitor->visitLeave($this);            
        }
        return parent::accept($visitor);
    }
}
