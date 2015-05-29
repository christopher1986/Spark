<?php

namespace Spark\Db\Adapter\Driver\Wpdb;

use Spark\Db\QueryBuilder;
use Spark\Db\Adapter\Driver\ConnectionInterface;

class Connection implements ConnectionInterface
{
    /**
     * A resource to connect with a database.
     *
     * @var wpdb
     */
    private $connection;
    
    /**
     * Create a new connection.
     *
     * @param wpdb $wpdb the resource to connect with the database.
     */
    public function __construct(\wpdb $wpdb)
    {
        $this->connection = $wpdb;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare($query)
    {
        return new Statement($this, $query);
    }
    
    /**
     * {@inheritDoc}
     */
    public function query($query)
    {
        $stmt = $this->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->connection;
    }
    
    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->connection->last_error;
    }
    
    /**
     * {@inheritDoc}
     */
    public function quote($input)
    {
        if (function_exists('esc_sql')) {
            return esc_sql($input);
        }
        return $wpdb->_escape($input);
    }
}
