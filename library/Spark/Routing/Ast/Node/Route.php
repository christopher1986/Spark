<?php

namespace Spark\Routing\Ast\Node;

use Spark\Routing\Ast\Node;
use Spark\Routing\Ast\Visitor\VisitorInterface;
use Spark\Routing\Ast\Visitor\HierarchicalVisitorInterface;

class Route extends Node
{
    /**
     * {@inheritDoc}
     */
    public function accept(VisitorInterface $visitor)
    {    
        if (($visitor instanceof HierarchicalVisitorInterface) && $visitor->visitEnter($this)) {            
            foreach ($this as $child) {
                $isValid = $child->accept($visitor);
                if ($isValid !== null && !$isValid) {
                    break;
                }
            }
            return $visitor->visitLeave($this);            
        }
        return parent::accept($visitor);
    }
}
