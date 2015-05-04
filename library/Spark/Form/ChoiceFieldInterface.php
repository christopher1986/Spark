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
 * The ChoiceFieldInterface represents a field with a predefined set of choices from which a user can choose. 
 * This interface does not specify how many choices can be selected because that logic should be implemented
 * by a concrete class. A value within a choice field is also known as a option. These options can be set using
 * {@link ChoiceFieldInterface::setOption($name, $value) or the {@link ChoiceFieldInterface::setOptions($options)} methods.
 *
 * Possible HTML elements that the ChoiceFieldInterface represents are drop-down list, multicheckbox and radio buttons. 
 * A ChoiceFieldInterface can be used to represent other HTML element as well. One should use this interface when a 
 * HTML element represents a set of predefined choices.
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
interface ChoiceFieldInterface
{
    /**
     * Set an option for this element under the given name. If this element previously contained an option
     * for the given name, the old option will be replaced.
     *
     * @param string $name the name of the option.
     * @param mixed $value the value of the option.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     */
    public function setOption($name, $value);
   
    /**
     * Set a collection of options. The options contained within the given collection will override any
     * existing option under the same name.
     *
     * @param array|Traversable one or more options to set.
     * @throws InvalidArgumentException if the given argument is an array or Traversable object.
     */
    public function setOptions($options);
    
    /**
     * Returns if present an option with the given name.
     *
     * @param string $name the name of an option to retrieve.
     * @return mixed|null the option found for the given name, or null if no option exists for the given name.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function getOption($name);
    
    /**
     * Returns a collection of all options contained by this element.
     *
     * @return array associative array of options.
     */
    public function getOptions();
    
    /**
     * Returns true if this element contains the specified option.
     *
     * @param string $name the name of an option whose presence will be tested.
     * @return bool true if an option with the given name exists, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */   
    public function hasOption($name);

    /**
     * Remove an option with the given name.
     *
     * @param string $name the name of an option to remove.
     * @return bool true if the given option exists and was removed, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function removeOption($name);
    
    /**
     * Remove the options contained within the given collection.
     *
     * @param array|Traversable $options the names of options to remove.
     * @return bool true if at least one option was removed, false otherwise.
     * @throws InvalidArgumentException if the given argument is an array or Traversable object.
     */
    public function removeOptions($options);

    /**
     * Remove all options. All options will be removed after this call returns.
     *
     * @return bool true if at least one option was removed, false otherwise.
     */
    public function clearOptions();
}
