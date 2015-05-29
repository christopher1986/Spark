<?php

namespace Spark\Db\Adapter\Driver\Wpdb;

interface ParameterInterface
{  
    /**
     * Returns the name of the parameter.
     *
     * @return string the name of the parameter.
     */
    public function getName();
    
    /**
     * Returns the value that the named parameter will be replaced with.
     *
     * @return mixed a scalar value or a collection of values.
     */
    public function getValue();
    
    /**
     * Returns data type of the value.
     *
     * @return int the data type.
     */
    public function getType();
}
