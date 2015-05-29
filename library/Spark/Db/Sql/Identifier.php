<?php

namespace Spark\Db\Sql;

/**
 * The Identifier class represents a SQL identifier for a database, tables or column.
 *
 * @author Chris Harris
 * @version 1.0.0
 * @link https://dev.mysql.com/doc/refman/4.1/en/identifiers.html
 */
class Identifier implements IdentifierInterface
{   
    /**
     * The identifier's name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Create a new column.
     *
     * @param string $name the column name.
     * @paration string $alias (optional) the alias.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Set the name for this identifier.
     *
     * @param string $name the identifier's name.
     * @throws InvalidArgumentException if the given argument is not a 'string' type.
     */
    public function setName($name)
    {
	    if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
	    }
    
        $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns a string representation of this expression.
     *
     * @return string a string representation of this expression.
     */
    public function __toString()
    {
        return $this->getName();
    }
}
