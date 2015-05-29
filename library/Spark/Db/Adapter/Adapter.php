<?php

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
