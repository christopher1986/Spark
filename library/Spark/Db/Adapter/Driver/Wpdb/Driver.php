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

namespace Spark\Db\Adapter\Driver\Wpdb;

use Spark\Db\Adapter\Driver\DriverInterface;
use Spark\Db\Adapter\Platform\Mysql;

/**
 *
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 0.0.1
 */
class Driver implements DriverInterface
{
    /**
     * A platform for a specific database type.
     *
     * @var PlatformInterface
     */
    private $platform;

    /**
     * Returns a connection for this driver.
     *
     * @return PlatformInterface a database platform.
     */
    public function getConnection()
    {
        return new Connection($GLOBALS['wpdb']);
    }
    
    /**
     * Returns the platform for a specific database type.
     *
     * @return PlatformInterface a database platform.
     */
    public function getPlatform()
    {
        if ($this->platform === null) {
            $this->platform = new Mysql();
        }
        
        return $this->platform;
    }
    
    /**
     * Tests whether the this driver is available.
     *
     * For example a driver that relies on a PHP extension such as mysqli or PDO will require
     * that these extensions have been loaded, otherwise this method will return false.
     *
     * @return bool true if the given driver is available and can be loaded, false otherwise.
     */
    public function isAvailable()
    {
        return (isset($GLOBALS['wpdb']) && $GLOBALS['wpdb'] instanceof \wpdb);
    }
    
    /**
     * Returns the name of the driver.
     *
     * @return string the driver name.
     */
    public function getName()
    {
        return 'wpdb';
    }
}
