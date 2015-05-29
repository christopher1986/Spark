<?php

namespace Spark\Db\Adapter\Driver;

interface StatementInterface
{  
    /**
     * Indicates a string value.
     *
     * @var int
     */
    const PARAM_STR = 0x01;
    
    /**
     * Indicates an integer value.
     *
     * @var int
     */
    const PARAM_INT = 0x02;
    
    /**
     * Indicates a float value.
     *
     * @var int
     */
    const PARAM_FLOAT = 0x04;

    /**
     * Fetches results as associative array.
     *
     * @var string
     */
    const FETCH_ASSOC = 'ARRAY_A';
    
    /**
     * Fetches results as numeric array.
     *
     * @var string
     */
    const FETCH_NUM = 'ARRAY_N';
    
    /**
     * Fetches results as anonymous object. 
     *
     * @var string
     */
    const FETCH_OBJ = 'OBJECT';
    
    /**
     * Executes a prepared statement. If the prepared statement includes parameters, you must
     * either call the {@link ConnectionInterface::bindParam} method to bind values to parameters
     * or pass an array containing key-values pairs where the keys match the the parameter name.
     *
     * @param array|null $params a collection of values to bind with the parameters, or null.
     * @return bool true on success or false on failure.
     */
    public function execute(array $params = null);
    
    /**
     * Fetches a single row from the resultset.
     *
     * @param string $type (optional) determines how the row is returned.
     * @param int $rowOffset (optional) the desired row, defaults to 0.
     * @return mixed a single row for this statement.
     */
    public function fetch($type = self::FETCH_OBJ, $rowOffset = 0);
    
    /**
     * Fetches the entire resultset.
     *
     * @param string $type (optional) determines how the resultset is returned.
     * @return mixed the entire resultset for this statement.
     */
    public function fetchAll($type = self::FETCH_OBJ);
    
    /**
     * Fetches a single column from the resultset.
     *
     * @param string $type (optional) determines how the column is returned.
     * @param int $columnOffset (optional) the desired column, defaults to 0.
     * @return mixed a single column for this statement.
     */
    public function fetchColumn($columnOffset = 0);
    
    /**
     * Returns the number of rows affected by the last query.
     *
     * @return int the number of rows.
     */
    public function rowCount();
    
    /**
     * Set the value for a named parameter.
     *
     * @param string $name the parameter name.
     * @param mixed $value the value to set.
     * @param type explicit data type for the parameter.
     * @link http://php.net/manual/en/pdostatement.bindvalue.php PDOStatement::bindValue
     */
    public function bindParam($name, $value, $type = self::PARAM_STR);
    
    /**
     * Unbind the value associated with the named parameter.
     *
     * @param string $name the parameter whose value to unbind.
     */
    public function unbindParam($name);
    
    /**
     * Tests whether a parameter exists for the given name.
     *
     * @param string $name the parameter name.
     * @return bool true if a parameter exists for the given name, false otherwise.
     */
    public function hasParam($name);
    
    /**
     * Remove all parameters. A statement will have no parameters anymore after this method returns.
     *
     * @return void
     */
    public function clearParams();
}
