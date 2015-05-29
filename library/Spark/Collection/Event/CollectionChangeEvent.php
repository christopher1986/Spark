<?php

namespace Spark\Collection\Event;

use Spark\EventDispatcher\Event;

/**
 * The CollectionChangeEvent class defines a change event for all collection type. Any observable 
 * collection type will notify it's listeners through a CollectionChangeEvent which inform those 
 * listeners what has changed to the collection.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class CollectionChangeEvent extends Event
{
    /**
     * The name of the event.
     *
     * @var string
     */
    const EVENT_CHANGED = 'collecion_changed';
    
    /**
     * Indicates an element has been added.
     *
     * @var int
     */
    const ADDED = 0x01;
    
    /**
     * Indicates an element has been removed.
     *
     * @var int
     */
    const REMOVED = 0x02;
    
    /**
     * The event source which sent the event.
     *
     * @var object
     */
    private $source;
    
    /**
     * The offset associated with an element.
     *
     * @var mixed;
     */
    private $offset;
    
    /**
     * The change type.
     *
     * @var int 
     */
    private $type;
    
    /**
     * Create a new event.
     *
     * @param object $source the event source which sent the event.
     * @param mixed $offset the offset associated with an element.
     * @param int $type the change type.
     */
    public function __construct($source, $offset, $type = self::ADDED)
    {
        $this->source = $source;
        $this->offset = $offset;
        $this->type = $type;
    }
    
    /**
     * Returns the source that sent the event.
     *
     * @return object an object that sent the event.
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Returns the offset for the element that triggered this event.
     *
     * @return mixed the offset associated with an element.
     */    
    public function getOffset()
    {
        return $offset;
    }
    
    /**
     * Returns the change type, which is either added or removed.
     *
     * @return int the change type.
     */
    public function getType()
    {
        return $this->type;
    }
}
