<?php

namespace Framework\Scanner;

/**
 * The TokenInterface describes the methods that allows a token to store a value and to identify itself.
 *
 * A token is created during the process of lexical analysis (tokenization). Lexical analysis is 
 * the process of converting a sequence of characters into a sequence of tokens.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
interface TokenInterface
{
    /**
     * Identifies an unknown token.
     *
     * @var string
     */
    const UNKNOWN = 'unknown';
    
    /**
     * Describes a token and hints what value might be stored inside the token.
     *
     * @return string
     */
    public function identify();
    
    /**
     * Returns the value stored by the token.
     *
     * Although the value stored by a token can be of any type, it's more likely that a token
     * will store a sequence of characters found through a process known as tokenization. 
     *
     * @return mixed the value stored by the token.
     */
    public function getValue();
    
    /**
     * The value to store by the token.
     *
     * @param mixed $value the value to store.
     * @see TokenInterface::getValue()
     */
    public function setValue($value);
}
