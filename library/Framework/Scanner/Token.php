<?php

namespace Framework\Scanner;

/**
 * A token stores meaningful character strings that are found when peforming lexical analysis.
 * 
 * A token should consists of a name by which it can be identified and an optional value. The name of
 * a token does not have to be unique amongst other tokens. The name of a token is simply used to hint 
 * what value is stored by the token. The value stored by a token can be of any type, but it's most 
 * likely that a token is used to stored a sequence of characters found with a lexer.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class Token implements TokenInterface
{
    /**
     * A name that identifies this token.
     *
     * @var string
     */
    private $identity;

    /**
     * The value stored by this token.
     * 
     * @var mixed
     */
    private $value;

    /**
     * Create a new token.
     *
     * @param string $identity a name that identifies this token.
     * @param mixed|null $value a value that will be stored by this token.
     */
    public function __construct($identity, $value = null)
    {
        $this->setIdentity($identity);
        $this->setValue($value);
    }
    
    /**
     * Set a name to identify this token.
     *
     * It's not uncommon for tokens to share the same identity. It's the value stored by a token that makes it's 
     * unique amongst other tokens. The name of a token is simply used to hint what value is stored by the token.
     *
     * @param string $identity a name that identifies this token.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function setIdentity($identity)
    {
        if (!is_string($identity)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($identity) ? get_class($identity) : gettype($identity))
            ));
        }
        
        $this->identity = $identity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function identify()
    {
        return $this->identity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
