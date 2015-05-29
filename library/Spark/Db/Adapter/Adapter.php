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

namespace Spark\Db\Adapter;

use Spark\Db\QueryBuilder;
use Spark\Db\Adapter\Driver\DriverInterface;

class Adapter implements AdapterInterface
{
    /**
     * The database driver.
     *
     * @var DriverInterface 
     */
    private $driver;
    
    /**
     * Create a new adapter.
     *
     * @param array|DriverInterface $driver a driver or options from which to create a driver.
     * @throws InvalidArgumentException if the first argument is a driver object, but does not implement the DriverInterface.
     */
    public function __construct($driver = array())
    {
        if (is_array($driver)) {
            $driver = $this->createDriver($driver);
        } else if (!($driver instanceof Driver\DriverInterface)) {
            throw new \InvalidArgumentException('The given driver does not imlement the "DriverInterface" interface.');
        }
        
        $this->setDriver($driver);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }
    
    /**
     * Set the database driver.
     *
     * @param DriverInterface $driver the database driver.
     * @throws RuntimeException if the given driver is not available.
     */
    private function setDriver(DriverInterface $driver)
    {
        if (!$driver->isAvailable()) {
            throw new \RuntimeException(sprintf(
                '%s: unable to load "%s" driver; make sure the necessary extensions have been loaded.',
                __METHOD__,
                $driver->getName()
            ));
        }
        
        $this->driver = $driver;
    }
    
    /**
     * Creates a new Driver from the given options.
     *
     * @param array $options the options from which to create the driver.
     * @return DriverInterface a driver object for the given options.
     */
    private function createDriver(array $options)
    {
        $defaults = array(
            'driver' => 'wpdb',
        );
        $options = array_merge($defaults, $options);
        
        // a driver has already been provided. 
        if ($options['driver'] instanceof DriverInterface) {
            return $options['driver'];
        } else if (!is_string($options['driver'])) {
            throw new \InvalidArgumentException('Unable to create driver; the driver name must be a fully qualified class name.');
        }
        
        $driverName = strtolower($options['driver']);
        switch ($driverName) {
            case 'wpdb':
            default:
                $driver = new Driver\Wpdb\Driver();
                break;
        }
        
        return $driver;
    }
}
