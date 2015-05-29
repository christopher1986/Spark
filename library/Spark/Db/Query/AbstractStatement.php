<?php

namespace Spark\Db\Query;

use Spark\Db\Adapter\AdapterAwareInterface;
use Spark\Db\Adapter\AdapterCapableInterface;
use Spark\Db\Adapter\AdapterInterface;
use Spark\Db\Sql\Limit;
use Spark\Db\Sql\Offset;

abstract class AbstractStatement implements AdapterAwareInterface, AdapterCapableInterface
{
    /**
     * A database adapter.
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Create a new statement.
     *
     * @param AdapterInterface $adapter a database adapter.
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setDbAdapter($adapter);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDbAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDbAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Returns a {@link PlatformInterface} object that contains platform specific informaion.
     *
     * @return PlatformInterface object containing information for a platform.
     */
    public function getPlatform()
    {
        return $this->adapter->getDriver()->getPlatform();
    }
    
    /**
     * Returns a {@link ConnectionInterface} object to communicate with the database.
     *
     * @return ConnectionInterface object to communicate with the database.
     */
    public function getConnection()
    {
        return $this->adapter->getDriver()->getConnection();
    }
    
    /**
     * Prepares an SQL statement for execution and returns a statement object.
     *
     * @return StatementInterface a {@link StatementInterface} object.
     * @see ConnectionInterface::prepare($sql)
     * @link http://php.net/manual/en/pdo.prepare.php
     */
    public function prepare()
    {
        return $this->getConnection()->prepare($this->getSqlString());
    }
    
    /**
     * Returns a list of columns using the platform column separator between each column.
     *
     * @param array $columns a collection of column names.
     * @return string a list of columns separated using a platform specific separator.
     * @see PlatformInterface::getColumnSeparator()
     */
    protected function listColumns(array $columns)
    {
        $separator = sprintf('%s ', $this->getPlatform()->getColumnSeparator());
        return implode($separator, $columns);
    }
    
    /**
     * Returns a limit clause for the given arguments.
     *
     * @param int $limit the number of results to retrieve.
     * @param int|null $offset (optional) the record to start retrieving results at.
     * @return string a LIMIT clause for this specific platform.
     * @see PlatformInterface::getLimitClause($limit, $offset)
     */
    protected function limitResults($limit = null, $offset = null)
    {
        return $this->getPlatform()->getLimitClause($limit, $offset);
    }
    
    /**
     * Returns the generated SQL string.
     *
     * @return string a SQL statement.
     */
    abstract public function getSqlString();
}
