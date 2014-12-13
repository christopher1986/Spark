<?php

namespace Framework\EventDispatcher;

/**
 * The EventDispatcherInterface allows a concrete class to become
 * a manager for different kind of events. Listeners can register
 * to a manager and will be notified by the manager when an event
 * is dispatched.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0 
 */
interface EventDispatcherInterface
{
    /**
     * Dispatch an event to all registered handlers.
     *
     * @param string $eventName the name of the event to dispatch.
     * @param Event|null the event to dispatch, if not supplied an empty Event will be created.
     * @return Event|null the event that was dispatched, or null if it was consumed by a filter or handler.
     */
    public function dispatch($eventName, Event $event = null);
    
    /**
     * Register a new filter for the specified event.
     *
     * @param string $eventName the name of an event to listen for.
     * @param callable $eventFilter the filter to register.
     * @param int $priority the higher the priority, the earlier the event filter will be triggered.
     */
    public function addEventFilter($eventName, $eventFilter, $priority = 0);
    
    /**
     * Unregister a filter for the specified event.
     *
     * @param string $eventName the event from which to unregister.
     * @param callable $eventFilter the filter to unregister.
     */
    public function removeEventFilter($eventName, $eventFilter);

    /**
     * Returns the filter of of the specied event, or all filters if the event is not provided.
     *
     * @param string $eventName the name of the event.
     * @return array the filter for the specified event, or all filters.
     */
    public function getEventFilters($eventName = null);

    /**
     * Returns true if at least one filter is registered for the specified event.
     *
     * @param string $eventName the name of the event.
     * @return bool true if the specified event has any filters, false otherwise.
     */
    public function hasEventFilters($eventName);
    
    /**
     * Register a new handler for the specified event.
     *
     * @param string $eventName the name of an event to listen for.
     * @param callable $eventHandler the handler to register.
     * @param int $priority the higher the priority, the earlier the handler will be triggered.
     */
    public function addEventHandler($eventName, $eventHandler, $priority = 0);
    
    /**
     * Unregister an handler for the specified event.
     *
     * @param string $eventName the event from which to unregister.
     * @param callable $eventHandler the handler to unregister.
     */
    public function removeEventHandler($eventName, $eventHandler);

    /**
     * Returns the handler of the specified event, or all handlers if the event is not provided.
     *
     * @param string $eventName the name of the event.
     * @return array the handler for the specified event, or all handlers.
     */
    public function getEventHandlers($eventName = null);

    /**
     * Returns true if at least one handler is registered for the specified event.
     *
     * @param string $eventName the name of the event.
     * @return bool true if the specified event has any handlers, false otherwise.
     */
    public function hasEventHandlers($eventName);
}
