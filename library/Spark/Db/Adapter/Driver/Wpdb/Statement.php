<?php

namespace Spark\Db\Adapter\Driver\Wpdb;

use Spark\Db\Adapter\Driver\StatementInterface;
use IteratorAggregate;

class Statement implements StatementInterface, IteratorAggregate
{    
    /**
     * A resource to connect with a database.
     *
     * @var wpdb
     */
    private $connection;

    /**
     * A collection of parameter objects.
     *
     * @var array
     */
    private $params = array();
    
    /**
     * A query that still needs to prepared.
     *
     * var string
     */
    private $rawQuery = '';
    
    /**
     * A query that already has been prepared.
     *
     * @var string
     */
    private $preparedQuery = '';

    /**
     * A flag that indicates if this statement has been prepared.
     * 
     * @var boolean
     */
    private $isPrepared = false;

    /**
     * Create a new connection.
     *
     * @param Connection $connection a {@link Connection} object to communicate with the data.
     * @param string $query the query to prepare.
     */
    public function __construct(Connection $connection, $query)
    {
        $this->connection = $connection;
        $this->rawQuery   = $query;
        $this->createParams($query);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $params = array())
    {
        $this->preparedQuery = $this->prepareQuery($this->rawQuery, $params);
        $this->isPrepared = true;
        
        var_dump($this->preparedQuery);
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetch($type = self::FETCH_OBJ, $rowOffset = 0)
    {
        $results = array();
        if ($this->isPrepared) {
            $results = $this->connection->getResource()->get_row($this->preparedQuery, $type, $rowOffset);
        }
            
        return $results;
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetchAll($type = self::FETCH_OBJ)
    {
        $results = array();
        if ($this->isPrepared) {
            $results = $this->connection->getResource()->get_results($this->preparedQuery, $type);
        }
        
        return $results;
    }
    
    /**
     * {@inheritDoc}
     */
    public function fetchColumn($columnOffset = 0)
    {
        $results = array();    
        if ($this->isPrepared) {
            $results = $this->connection->getResource()->get_col($this->preparedQuery, $columnOffset);
        }
        
        return $results;
    }
    
    /**
     * {@inheritDoc}
     */
    public function rowCount()
    {
        return $this->connection->getResource()->rows_affected;
    }
    
    /**
     * {@inheritDoc}
     */
    public function bindParam($name, $value, $type = Parameter::PARAM_STR)
    {
        $param = $this->getParam($name);
        if ($param !== null) {
            $param->setType($type);
            $param->setValue($value);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function unbindParam($name)
    {
        $param = $this->getParam($name);
        if ($param !== null) {
            $param->reset();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasParam($name)
    {
        return (isset($this->params[$name]));
    }
    
    /**
     * {@inheritDoc}
     */
    public function clearParams()
    {
        foreach ($this->params as $param) {
            $param->reset();
        }
    }
    
    /**
     * Returns if present the parameter for the given name.
     *
     * @param string $name the name of the parameter to retrieve.
     * @return Parameter|null the parameter for the given name, or null on failure.
     */
    public function getParam($name)
    {
        $param = null;
        if ($this->hasParam($name)) {
            $param = $this->params[$name];
        }
        
        return $param;
    }
    
    /**
     * Returns an external iterator over the resultset of this statement in proper sequence. 
     *
     * @return ArrayIterator an iterator over the resultset of this statement.
     */
    public function getIterator()
    {
        $data = $this->fetchAll();
        return new ArrayIterator($data);
    }
    
    /**
     * Creates a collection of parameters found within the given query. A query can 
     * contain zero or more named (:name) parameter markers for which real values 
     * will be substituted when the statement is executed.
     *
     * @param string $query the query whose parameters to collect.
     */
    private function createParams($query)
    {
        $params = array();
        if (preg_match_all('/(\:[a-z0-9_-]+)/i', $query, $matches)) {
            $names = array_unique($matches[1]);
            foreach ($names as $name) {
                $params[$name] = new Parameter($name);
            }
        }
        
        $this->params = $params;
    }
        
    /**
     * Returns a query where the given parameter is prepared.
     * 
     * @param string $query the query which contains the parameter.
     * @param array $params a collection of {@link Parameter} objects.
     * @return string a query where the given parameter placeholders has been prepared.
     * @see wpdb:prepare($query, $args);
     * @link https://codex.wordpress.org/Function_Reference/esc_sql
     * @link https://codex.wordpress.org/Class_Reference/wpdb#Protect_Queries_Against_SQL_Injection_Attacks
     */
    private function prepareQuery($query, array $params)
    {
        $args = array();
        $params = $this->mergeWithBoundedParams($params);    
        foreach ($params as $name => $param) {
            // corrects formatting errors similar to the wpdb class.
            if ($param->getType() === Parameter::PARAM_STR) {
                $query = str_replace("'{$name}'", "{$name}", $query);
                $query = preg_replace("/(?!\")(?<!\"){$name}/", "\"{$name}\"", $query);
            }
            
            // escape characters if needed.
            $value = $this->connection->quote($param->getValue());
            if (is_array($value)) {
                $glue  = ($param->getType() === Parameter::PARAM_STR) ? '", "' : ',';
                $value = implode($glue, $value);
            }
            
            $args[$name] = $value;
        }

        return $this->format($query, $args);
    }
    
    /**
     * Merges the given parameters with the already bounded parameters.
     *
     * @param array $params one or more parameters to merge.
     * @param int $type the type of values contained by the given parameters.
     * @return array the merger of the given parameters with the already bounded parameters.
     * @throws RuntimeException if the given parameters contains keys for which there are no parameters.
     */
    private function mergeWithBoundedParams(array $params, $type = Statement::PARAM_STR)
    {
        $diff = array_diff_key($params, $this->params);
        if (!empty($diff)) {
            throw new \RuntimeException(sprintf(
                '%s: the array provided contains keys for which there are no parameters',
                __METHOD__
            ));
        }
        
        foreach ($params as $name => $value) {
            $params[$name] = new Parameter($name, $value, $type);
        } 
        return array_merge($this->params, $params);
    }
    
    /**
     * Returns a formated string.
     *
     * @param string $string the format string.
     * @param array $replacements the values to replace the placeholders with.
     * @return string a string produced according to the formatting string.
     * @link https://gist.github.com/codearachnid/4462713
     */
    private function format($string = '', array $replacements = array()) 
    {
        if (!$string) {
            return '';
        }
        
        if (is_array($replacements) && count($replacements) > 0) {
            foreach ($replacements as $name => $value) {
                $string = str_replace($name, $value, $string);
            }
        }
        return $string;
    }
}
