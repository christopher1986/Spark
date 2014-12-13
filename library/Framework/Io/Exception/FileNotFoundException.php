<?php

use Framework\Io\Exception;

class FileNotFoundException extends IOException
{
    public function __construct($path, $message, $code = 0, \Exception $previous = null)
    {
        $errorMsg = sprintf('path: %s, reason: %s', $path, $message);
        parent::__construct($errorMsg, $code, $previous);
    }
}
