<?php

namespace Framework\EventDispatcher;

interface EventDispatcherAwareInterface extends EventsCapableInterface
{
    /**
     * Set an event manager that allows dispatching of events.
     *
     * @param EventDispatcherInterface an event manger that can dispatch events.
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher);
}
