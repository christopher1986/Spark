<?php

namespace Spark\Routing\Ast\Node;

use Spark\Routing\Ast\Node;
use Spark\Routing\Ast\VisitorInterface;

class Text extends Node
{
    /**
     * The text contained by this node.
     *
     * @var string
     */
    private $text;

    /**
     * Construct a new TextNode.
     *
     * @param string $text the text.
     */
    public function __construct($text)
    {
        $this->setText($text);
    }
    
    /**
     * Set the text.
     *
     * @param string $text the text.
     */
    public function setText($text)
    {
        $this->text = $text;
    }
    
    /**
     * Returns the text.
     *
     * @return string the text.
     */
    public function getText()
    {
        return $this->text;
    }
}
