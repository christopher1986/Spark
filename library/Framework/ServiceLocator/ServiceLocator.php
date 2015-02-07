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
 * The ServiceLocator encapsulates one or more services and when requested returns an instance of that service. 
 * By default all services are shared, this means that when a service is requested multiple times the service 
 * locator will return the same instance of that service. 
 *
 * The service locator however is capable of instantiating a new service for each request but this does require
 * you to call the {@link ServiceLocator::shared($name, $share)} method for that particular service.
 *
 * @autor Chris Harris
 * @version 1.0.0
 */
class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * Callables that create a service. 
     *
     * @var array
     */
    private $factories = array();
    
    /**
     * Class names of services to instantiate.
     *
     * @var arry
     */
    private $invokables = array();
    
    /**
     * Services that have already been instantiated.
     *
     * @var array
     */
    private $instances = array();
    
    /**
     * Defines if a service is shared.
     *
     * @var bool
     */
    private $shared = array();
    
    /**
     * Register a factory with the service locator. The following code demonstrates
     * how a factory can be be registered with the service locator.
     *
     * $sl = new ServiceLocator();
     * $sl->factory('Service', function($sl) {
     *     return new Framework\Service\SomeService();
     * });
     *
     * @param string $name the name under which to register the factory.
     * @param callable an anonymous function or closure.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     * @throws InvalidArgumentException if the second argument is not a callable function.
     * @return ServiceLocator
     */
    public function factory($name, $factory)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        } else if (!is_callable($factory)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a callable as argument; received "%s"',
                __METHOD__,
                (is_object($factory) ? get_class($factory) : gettype($factory))
            ));
        }
        
        // store factory service.
        $this->factories[$name] = $factory;
        // make service shared.
        $this->shared($name);
        
        return $this;
    }
    
    /**
     * Register an invokable class with the service locator. The following code demonstrates
     * how an invokable service can be registered with the service locator.
     *
     * $sl = new ServiceLocator();
     * $sl->invokable('Service', 'Framework\Service\SomeService');
     *
     * @param string $name the name under which to register the invokable.
     * @param string $invokable a fully qualified class name.
     * @throws InvalidArgumentException if the first argument is not of type 'string'.
     * @throws InvalidArgumentException if the second argument is not of type 'string'.
     * @return ServiceLocator
     */
    public function invokable($name, $invokable)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        } else if (!is_string($invokable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a class name as argument; received "%s"',
                __METHOD__,
                (is_object($invokable) ? get_class($invokable) : gettype($invokable))
            ));
        }
        
        // store invokable service.
        $this->invokables[$name] = $invokable;
        // make service shared.
        $this->shared($name);
        
        return $this;
    }
    
    /**
     * Register a service with the service locator. The following code demonstrates
     * how a service can be registered with the service locator.
     *
     * $sl = new ServiceLocator();
     * $sl->service('Service', new SomeService());
     *
     * @param string $name the name of the service to register.
     * @param mixed $service the service to register.
     * @throws LogicException if a service is already registered under the given name.
     * @return ServiceLocator
     */
    public function service($name, $service)
    {
        if ($this->has($name)) {
            throw new \LogicException(sprintf(
                'A service or factory has already been registered under the given name "%s"',
                $name
            ));
        }
        
        // register (instantiated) service.
        $this->instances[$name] = $service;
        // inject service locator with service.
        if ($service instanceof ServiceLocatorAwareInteface) {
            $service->setServiceLocator($this);
        }
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($name)
    {        
        if (!$this->has($name)) {
            throw new \LogicException(sprintf(
                'No service registered under the given name "%s"',
                $name
            ));
        }
    
        $instance = null;
        if (isset($this->instances[$name])) {
            $instance = $this->instances[$name];
        } else {        
            // create service.
            $instance = $this->create($name);
            // if shared store service.
            if (isset($this->shared[$name]) && $this->shared[$name]) {
                $this->instances[$name] = $instance;
            }
        }
        
        return $instance;
    }
    
    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
	    }
    
        if (isset($this->factories[$name])) {
            return true;
        } else if (isset($this->invokables[$name])) {
            return true;
        } else if (isset($this->instances[$name])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Indicates whether a service under the given name is shared.
     *
     * When a service is not shared a new instance will be created every time the
     * service is called, otherwise one instance of the service will be created
     * and shared amongst other services.
     *
     * @param string $name the name under which a service is registered.
     * @param bool $shared whether the service is shared or not.
     * @throws LogicException if a non-existing name is provided.
     */
    public function shared($name, $shared = true)
    {
        if (!$this->has($name)) {
            throw new \LogicException(sprintf(
                'No service registered under the given name "%s"',
                $name
            ));
        }
    
        if (((bool) $shared) === false && isset($this->instances[$name])) {
            unset($this->instances[$name]);
        }
        
        $this->shared[$name] = (bool) $shared;
    }
    
    /**
     * Creates a new instance for the service registered under the given name.
     *
     * @param string $name the name under which a service is registered.
     * @return mixed a new instance for the service found, or null on failure.
     * @throws LogicException if an invokable service could not be instantiated.
     */
    private function create($name)
    {
        $instance = null;
        if (isset($this->factories[$name])) {
            $factory = $this->factories[$name];
            $instance = call_user_func($factory, $this);
        }
        
        if ($instance === null && isset($this->invokables[$name])) {
            $invokable = $this->invokables[$name];
            if (!class_exists($invokable)) {
                throw new \LogicException(sprintf(
                    '%s: no valid instance found; received the following service: "%s"',
                    __CLASS__,
                    $invokable
                ));
            }
            
            $instance = new $invokable();
            if ($instance instanceof ServiceLocatorAwareInteface) {
                $instance->setServiceLocator($this);
            }
        }
        
        return $instance;
    }
}
