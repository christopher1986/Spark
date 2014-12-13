<?php

namespace Framework\Scanner;

/**
 * The ScannerInterface defines the methods required for a scanner to
 * process a sequence of characters, it being from a string, file or stream.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
interface ScannerInterface
{
    
    /**
     * Scans the given string and returns an array of tokens for uri parts found in that string.
     *
     * @return array an array consisting of tokens that were found.
     */
    public function scan();
}
