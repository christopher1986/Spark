<?php

namespace Spark\Db\Adapter\Driver\Wpdb;

use Spark\Db\Adapter\Driver\DriverInterface;
use Spark\Db\Adapter\Platform\Mysql;

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
