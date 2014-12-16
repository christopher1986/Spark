<?php

namespace Framework\Common;

/**
 * The Equatable interface allows an object to be tested for equality with another object.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface Equatable
{
    /**
     * Indicates whether this object is considered equal to another object.
     *
     * @param mixed $obj the object for which equality should be tested.
     * @return bool true if this object is equal to the given object, false otherwise.
     */
    public function equals($obj);
}
