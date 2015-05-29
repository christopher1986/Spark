<?php

namespace Spark\Db\Query;

interface OffsetCapableInterface
{
    /**
     * Specifies the offset of the results to return. Passing a 'null' literal will 
     * remove any previously set offset. 
     *
     * Using this method in conjunction with the {@link OrderCapableInterface::limit($limit)} 
     * method allows pagination to be applied on the results.
     *
     * @param int|null $offset the offset at which the returning results will start.
     */
    public function offset($offset = null);
}
