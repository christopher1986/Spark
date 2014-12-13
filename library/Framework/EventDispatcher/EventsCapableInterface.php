<?php

namespace Framework\EventDispatcher;

/**
 * The EventsCapableInterface listener allow classes to delegate
 * events to a concrete event manager which is then responsible 
 * for dispatching events to listeners.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0 
 */
interface EventsCapableInterface
{
    /**
     * Returns an event dispatcher, or lazy-loads a
     * new {@link EventDispatcher } if one is not registered.
     *
     * @return EventDispatcherInterface returns the event manager.
     */
    public function getEventDispatcher();
}
