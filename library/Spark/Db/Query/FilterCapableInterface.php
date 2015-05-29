<?php

namespace Spark\Db\Query;

interface FilterCapableInterface
{
    /**
     * Add one or more restrictions for the returned results, and creates a 
     * logical 'AND' relation with any previous restrictions. Replaces any
     * previously restrictions that were set.
     *
     * @param string|array $where one or more restrictions.
     */
    public function where($where);

    /**
     * Add one or more restrictions for the returned results, and creates a 
     * logical 'AND' relation with any previous restrictions.
     *
     * @param string|array $where one or more restrictions.
     */
    public function andWhere($where);
    
    /**
     * Add one or more restrictions for the returned results, and creates a 
     * logical 'OR' relation with any previous restrictions.
     *
     * @param string|array $where one or more restrictions.
     */
    public function orWhere($where);
}
