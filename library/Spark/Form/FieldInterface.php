<?php
/**
 * Copyright (c) 2015, Chris Harris.
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
 * @copyright  Copyright (c) 2015 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Form;

/**
 * A form field is a element which allows interaction with the user. Most fields store information (data) that is provided
 * by the user. The methods {@link FieldInterface::setValue($value)} and {@link FieldInterface::getValue()} can be used to 
 * either set or retrieve that information. Some but not all fields have conditional logic which places the field into an invalid
 * or valid state. Use the following methods {@FieldInterface::addMessage($message) or {@FieldInterface::addMessages($messages) 
 * to inform the user of the errors that have placed the field into an invalid state.
 * 
 * @author Chris Harris
 * @version 1.0.0
 */
interface FieldInterface extends ElementInterface
{
    /**
     * Set the value for this field.
     *
     * @param mixed $value the value to set.
     */
    public function setValue($value);
    
    /**
     * Returns the value contained by this field.
     *
     * @return mixed the value.
     */
    public function getValue();
    
    /**
     * Add a message to this field.
     *
     * @param string $message the message to add.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function addMessage($message);
    
    /**
     * Add a collection of messages.
     *
     * @param array|Traversable $message a collection of messages to add.
     */
    public function addMessages($messages);
    
    /**
     * Returns a collection of messages.
     *
     * @return array|null a numeric array of messages, or null if this field has no messages.
     */
    public function getMessages();
    
    /**
     * Returns true if this field has at least one message.
     *
     * @return bool true if this field has at lease one message, false otherwise.
     */   
    public function hasMessages();
    
    /**
     * Remove all messages. All messages will be removed after this call returns.
     *
     * @return bool true if at least one message was removed, false otherwise.
     */
    public function clearMessages();
}
