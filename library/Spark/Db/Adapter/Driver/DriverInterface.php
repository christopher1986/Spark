<?php

namespace Spark\Db\Adapter\Driver;

interface DriverInterface
{
    /**
     * Returns a database connection for this driver.
     *
     * @return ConnectionInterface a database connection.
     */
    public function getConnection();
    
    /**
     * Returns the database platform associated with this driver.
     *
     * @return PlatformInterface a database platform.
     */
    public function getPlatform();
    
    /**
     * Tests whether the this driver is available.
     *
     * For example a driver that relies on a PHP extension such as mysqli or PDO will require
     * that these extensions have been loaded, otherwise this method will return false.
     *
     * @return bool true if the given driver is available and can be loaded, false otherwise.
     */
    public function isAvailable();
    
    /**
     * Returns the name of the driver.
     *
     * @return string the driver name.
     */
    public function getName();
}
