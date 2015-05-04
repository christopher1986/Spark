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

namespace Spark\EventDispatcher;

/**
 * The Event class contains event data that a dispatcher can send to any listener. More specific events can subclass this 
 * base class to add specific information to event being dispatched.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Event
{
    /**
     * a flag that determines if this event is consumed.
     *
     * @var bool.
     */
    protected $consumed = false;
    
    /**
     * the dispatcher from which this event was dispatched.
     * 
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    
    /**
     * the name of this event.
     * 
     * @var string
     */
    protected $name;

    /**
     * Construct a new event that can be dispatched to listeners.
     *
     * @param string $name the name of this event.
     * @param EventDispatcherInterface $dispatcher the dispatcher from which this event was dispatched.
     */
    public function __construct($name = null, EventDispatcherInterface $dispatcher = null)
    {
        if ($name !== null) {
         $this->setName($name);
        }
        
        if ($dispatcher !== null) {
            $this->setDispatcher($dispatcher);
        }
    }
    
    /**
     * Returns true if this event has been consumed and it should not propagate any further.
     *
     * @return bool true if the event has been consumed, false otherwise.
     */
    public function isConsumed()
    {
        return $this->consumed;
    }
    
    /**
     * Consume the event and stop it from propagating any further.
     */
    public function consume()
    {
        $this->consumed = true;
    }
    
    /**
     * Set the dispatcher from which this event was dispatched.
     *
     * @param EventDispatcherInterface $dispatcher the dispatcher from which this event was dispatched.
     */ 
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Get the dispatcher from which this event was dispatched.
     *
     * @return EventDispatcherInterface returns the dispatcher from which this event was dispatched.
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
    
    /**
     * Set the name for this event.
     *
     * @param string $name the name of this event.
     */
    public function setName($name)
    {
        if (!is_null($name) && !is_string($name)) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s must be of the type string, %s given', __METHOD__, gettype($name)));
        }
        
        $this->name = $name;
    }
    
    /**
     * Returns the name for this event.
     *
     * @return string the name of this event.
     */
    public function getName()
    {
        return $this->name;
    }
}
