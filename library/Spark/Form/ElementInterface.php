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
 * A form element also known as a graphical user interface element. Most HTML elements support interaction with the
 * user and allow the information (data) entered by the user to be submitted and processed by the server.
 *
 * The ElementInterface describes the methods that as whole allow a HTML element to be constructed, which in turn
 * then allows the browser to render the form element on the screen. A concrete class that implements the
 * ElementInterface may choose or require more information in order to create a more complex HTML element.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ElementInterface
{
    /**
     * Set the name of this element.
     *
     * @param string $name the name to set.
     */
    public function setName($name);
    
    /**
     * Returns the name of this element.
     *
     * @return string the name.
     */
    public function getName();
    
    /**
     * Set an attribute for this element under the given name. If this element previously contained an attribute
     * for the given name, the old attribute will be replaced.
     *
     * @param string $name the name of the attribute.
     * @param mixed $value the value of the attribute.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    public function setAttribute($name, $value);
    
    /**
     * Set a collection of attributes. The attributes contained within the given collection will override any
     * existing attribute under the same name.
     *
     * @param array|Traversable one or more attributes to set.
     * @throws InvalidArgumentException if the given argument is an array or Traversable object.
     */
    public function setAttributes($attributes);
    
    /**
     * Returns if present an attribute with the given name.
     *
     * @param string $name the name of the attribute to retrieve.
     * @return mixed|null the attribute found for the given name, or null if no attribute exists for the given name.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function getAttribute($name);
    
    /**
     * Returns a collection of all attributes contained by this element.
     *
     * @return array associative array of attributes.
     */
    public function getAttributes();
 
    /**
     * Returns true if this element contains the specified attribute.
     *
     * @param string $name the name of the attribute whose presence will be tested.
     * @return bool true if an attribute with the given name exists, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */   
    public function hasAttribute($name);
    
    /**
     * Remove the attribute with the given name.
     *
     * @param string $name the name of the attribute to remove.
     * @return bool true if the given attribute exists and was removed, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function removeAttribute($name);
    
    /**
     * Remove the attributes contained within the given collection.
     *
     * @param array|Traversable $attributes the names of the attributes to remove.
     * @return bool true if at least one attribute was removed, false otherwise.
     * @throws InvalidArgumentException if the given argument is an array or Traversable object.
     */
    public function removeAttributes($attributes);
    
    /**
     * Remove all attributes. All attributes will be removed after this call returns.
     *
     * @return bool true if at least one attribute was removed, false otherwise.
     */
    public function clearAttributes();
}
