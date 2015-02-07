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

namespace Framework\ServiceLocator;

/**
 * The ServiceLocatorInterface describes the methods required to determine whether a service
 * is known to the service locator and allows you to retrieve a service from the service locator.
 *
 * This interfaces however does not define how services are registered with a service locator.
 * Each service locator will define it's own strategy for creating a service. For instance a service
 * locator might allow a service to be defined as an invokable while another service locator might use
 * a factory, or a service locator might implement both strategies or a completely different strategy.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ServiceLocatorInterface
{
    /**
     * Returns a service for the given name.
     *
     * @param string $name the name under a which a service is registered.
     * @return mixed an instance of the service.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     * @throws LogicException if a non-existing name is provided.
     */
    public function get($name);
    
    /**
     * Returns true if a service is defined for the given name.
     *
     * @param string $name the name under which a service is registered.
     * @return bool true if service is registered, false otherwise.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function has($name);
}
