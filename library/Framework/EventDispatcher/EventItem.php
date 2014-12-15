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

use Framework\Sort\Comparable;

/**
 * The EventItem is reponsible for storing event listeners. The priority of an EventItem is used to determine it's position 
 * within a collection that is sorted.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class EventItem implements Comparable
{    
    /**
     * A listener to be triggered for specific event(s).
     *
     * @var callable
     */
    private $listener;

    /**
     * The priority of the listener.
     *
     * @var int
     */
    private $priority;
    
    /**
     * Construct a new EventItem.
     *
     * @param callable $listener the listener to store.
     * @param int $priority the priority of the listener.
     */
    public function __construct($listener, $priority = 0)
    {
        $this->setListener($listener);
        $this->setPriority($priority);
    }
    
    /**
     * Set the listener.
     *
     * @param callable $listener a listener that is triggered for specific event(s).
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
    }
    
    /**
     * Returns the listener.
     *
     * @return callable a listener that is triggered for specific event(s).
     */
    public function getListener()
    {
        return $this->listener;
    }
    
    /**
     * Set the priority of the listener.
     *
     * @param int $priority the priority of the listener, defaults to 0.
     * @throws InvalidArgumentException if the given argument is not numeric.
     */
    public function setPriority($priority = 0) 
    {
        if (!is_numeric($priority)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($priority) ? get_class($priority) : gettype($priority))
            ));
        }
        
        $this->priority = (int) $priority;
    }
    
    /**
     * Returns the priority of the listener.
     *
     * @return int the priority of the listener.
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * {@inheritDoc}
     */
    public function compareTo($obj)
    {
        if ($obj instanceof EventItem) {
            if ($obj->getPriority() == $this->getPriority()) {
                return 0;
            }
            return ($this->getPriority() > $obj->getPriority()) ? 1 : -1;
        }
        return 0;
    }
}
