<?php

namespace Framework\Scanner;

use Framework\Collection\ArrayList;

/**
 * A TokenCollection is a list which also allows you to peek at a token that lies beyond the "current" token.
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
class TokenCollection extends ArrayList
{
    /**
     * Returns a token that lies beyond the "current" token without moving the internal pointer forward.
     *
     * @param int $lookahead the number of tokens to look forward.
     * @return null|mixed the token found, or null if lookahead is larger than the number of tokens in this collection.
     * @throws InvalidArgumentException if the given argument is not an integer value.
     */
    public function peek($lookahead = 1)
    {
	    if (!is_int($lookahead)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($lookahead) ? get_class($lookahead) : gettype($lookahead))
            ));
	    }

        $element = null;
        if (($index = $this->key()) >= 0 && is_int($index)) {
            $index = (int) ($index + $lookahead);
            if ($index < $this->count()) {
                $element = $this->get($index);
            }
        }
        return $element;
    }
}
