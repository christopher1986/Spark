<?php

namespace Spark\Db\Sql;

/**
 * The From class represents the table to retrieve results from. To accomplish that purpose
 * it decorates an object that implements the {@link IdentifierInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class From
{   
    /**
     * The identifier.
     *
     * @var IdentifierInterface
     */
    private $table;
    
    /**
     * The alias.
     *
     * @var string
     */
    private $alias = '';

    /**
     * Create a new alias.
     *
     * @param string $table the table name.
     * @paration string $alias (optional) the alias.
     */
    public function __construct($table, $alias = '')
    {
        $this->setTable($table);
        $this->setAlias($alias);
    }

    /**
     * Set the table name.
     *
     * @param string $table the table name
     */
    public function setTable($table)
    {    
	    if (!is_string($table)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($table)) ? get_class($table) : gettype($table)
            ));
	    }
    
        $this->table = $table;
    }
    
    /**
     * Returns the table name.
     *
     * @return string the table name.
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Set the alias name.
     *
     * @param string $alias the alias.
     * @throws InvalidArgumentException if the given argument is not a 'string' type.
     */
    public function setAlias($alias)
    {
	    if (!is_string($alias)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($alias)) ? get_class($alias) : gettype($alias)
            ));
	    }
    
        $this->alias = $alias;
    }
    
    /**
     * Returns the alias.
     *
     * @return string the alias.
     */
    public function getAlias()
    {
        return $this->alias;
    }
    
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        $from = sprintf('FROM %s', $this->getTable());
        if (($alias = $this->getAlias()) !== '') {
            $from = sprintf('%s %s', $from, $alias);
        }
    
        return $from;
    }
}
