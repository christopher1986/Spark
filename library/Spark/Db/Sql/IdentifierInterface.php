<?php 

namespace Spark\Db\Sql;

interface IdentifierInterface
{
    /**
     * Returns the name of the identifier.
     *
     * @return string the identifier's name.
     */
    public function getName();
}
