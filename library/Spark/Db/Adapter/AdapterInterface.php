<?php

namespace Spark\Db\Adapter;

interface AdapterInterface
{
    /**
     * Returns a driver for a specific database server.
     *
     * @return DriverInterface object to communicate with the database.
     * @link http://php.net/manual/en/mysqli.overview.php
     */
    public function getDriver();
    
    /**
     * Returns a {@link QueryBuilder} for this connection.
     *
     * @return QueryBuilder a new query builder.
     */
    public function getQueryBuilder();
}
