<?php

namespace Spark\Db\Sql;

/**
 * The As class represents an alias for a SQL identifier. To accomplish that purpose
 * it decorates an object that implements the {@link IdentifierInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class Alias 
{   
    /**
     * The identifier.
     *
     * @var IdentifierInterface
     */
    private $identifier;
    
    /**
     * The alias.
     *
     * @var string
     */
    private $alias = '';

    /**
     * Create a new alias.
     *
     * @param string $identifier the identifier for which this alias is created.
     * @paration string $alias the alias.
     */
    public function __construct(IdentifierInterface $identifier, $alias)
    {
        $this->setIdentifier($identifier);
        $this->setAlias($alias);
    }

    /**
     * Set the identifier this alias represents.
     *
     * @param IdentifierInterface $identifier the identifier this alias represents.
     */
    public function setIdentifier(IdentifierInterface $identifier)
    {    
        $this->identifier = $identifier;
    }
    
    /**
     * Returns the identifier this alias represents.
     *
     * @var IdentifierInterface the identifier.
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
        return sprintf('%s AS %s', (string) $this->getIdentifier(), $this->getAlias());
    }
}
