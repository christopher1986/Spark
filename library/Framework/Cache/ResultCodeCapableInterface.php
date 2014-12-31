<?php

namespace Framework\Cache;

/**
 * A storage that is capable of storing a result code for the last operation should implement this interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ResultCodeCapableInterface
{    
    /**
     * Returns the result code of the last operation.
     *
     * @return int a result code.
     */
    public function getResultCode();
}
