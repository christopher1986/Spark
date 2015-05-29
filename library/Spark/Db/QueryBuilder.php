<?php

namespace Spark\Db;

use Spark\Db\Adapter\AdapterInterface;
use Spark\Db\Query\Select;

class QueryBuilder implements QueryBuilderInterface
{
    /**
     * A database adapter.
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Create a new query builder.
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
    public function select($select)
    {
        $selects = (is_array($select)) ? $select : func_get_args();

        $statement = new Select($this->adapter);
        $statement->select($selects);
        
        return $statement;
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
}
