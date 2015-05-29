<?php

namespace Spark\Db\Adapter\Driver;

interface ConnectionInterface
{
    /**
     * Prepares a SQL statement for execution.A prepared statement will be safe from SQL injections 
     * because all parameters will be properly escaped.
     *
     * @param string $query the SQL statement to prepare.
     * @return StatementInterface a StatementInterface object.
     * @link https://codex.wordpress.org/Class_Reference/wpdb#Protect_Queries_Against_SQL_Injection_Attacks
     */
    public function prepare($query);
    
    /**
     * Executes a SQL statement. To protect against SQL injection the data inside the statement must
     * be properly escaped. As an alternative the {@link ConnectionInterface::prepare} method can be
     * used which will ensure that all parameters are properly escaped.
     *
     * @param string $query a properly escaped SQL statement.
     * @link https://codex.wordpress.org/Class_Reference/wpdb#Running_General_Queries
     */
    public function query($query);
    
    /**
     * Returns the resource that communicates with the underlying data source.
     *
     * @return mixed the object that communicates with the underlying data source.
     */
    public function getResource();
    
    /**
     * Returns the id of last inserted row.
     *
     * @return mixed the id of last insered row.
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     */
    public function lastInsertId();
    
    /**
     * Returns the last error information from the database resource.
     *
     * @return string the last error information.
     */
     public function errorInfo();
     
     /**
      * Places quotes around the input string (if required) and escapes special characters within 
      * the input string.
      *
      * @param string $input the string to be quoted.
      * @return a quoted string that is theoretically safe to pass into an SQL statement.
      */
     public function quote($input);
}
