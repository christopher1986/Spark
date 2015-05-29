<?php

namespace Spark\Db;

use Spark\Db\Adapter\AdapterAwareInterface;
use Spark\Db\Adapter\AdapterCapableInterface;

interface QueryBuilderInterface extends AdapterAwareInterface, AdapterCapableInterface
{
    /**
     * Creates a Select statement for the given columns.
     *
     * @param string|array|Traversable $select either a string for a single column or a collection for multiple columns.
     * @return Select An object to retrieve information from the underlying data source.
     */
    public function select($select);
}
