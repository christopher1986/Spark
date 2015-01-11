<?php

namespace Framework\Cache;

/**
 * A storage that is capable of storing a result message for the last operation should implement this interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ResultMessageCapableInterface
{    
    /**
     * Returns the result message of the last operation.
     *
     * @return int a result message.
     */
    public function getResultMessage();
}
