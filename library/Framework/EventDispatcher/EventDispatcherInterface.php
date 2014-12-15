<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

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
