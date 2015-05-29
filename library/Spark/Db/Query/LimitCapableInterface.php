<?php

namespace Spark\Db\Query;

interface LimitCapableInterface
{
    /**
     * Specifies the amount of results to return. Passing a 'null' literal will 
     * remove any previously set limit.
     *
     * @param int|null $limit the number of result to return.
     */
    public function limit($limit = null);
}
