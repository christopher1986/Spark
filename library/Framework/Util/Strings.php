<?php
/**
 * Copyright (c) 2014, Chris Harris.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Chris Harris <c.harris@hotmail.com>
 * @copyright  Copyright (c) 2014 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Framework\Util;

/**
 * This class contains various methods for altering or testing a string.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class Strings
{
    /**
     * Returns true if the given string starts with the specified prefix, otherwise false.
     *
     * @param string $haystack the string to search in.
     * @param string $needle the prefix to search for.
     * @return bool returns true if the character sequence represented by the argument is a 
     *              prefix of the character sequence represented by this string; false otherwise.
     * @link http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php#answer-834355
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    
    /**
     * Returns true if the given string ends with the specified suffix, otherwise false.
     *
     * @param string $haystack the string to search in.
     * @param string $needle the suffix to search for.
     * @return bool returns true if the character sequence represented by the argument is a 
     *              suffix of the character sequence represented by this string; false otherwise.
     * @link http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php#answer-834355
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
    
    /**
     * Returns a string where the given char is appended to the string, if the string
     * already contains the given char it is left unchanged.
     *
     * @param string $string the string to which the char will appended.
     * @param strign $char the character to append.
     * @return returns a string with the given character added to it.
     */
    public static function addTrailing($string, $char)
    {
        return rtrim($string, $char) . $char;
    }
    
    /**
     * Returns a string where the given char is prepended to the string, if the string
     * already contains the given char it is left unchanged.
     *
     * @param string $string the string to which the char prepended.
     * @param strign $char the character to prepend.
     * @return returns a string with the given character prepended to it.
     */
    public static function addLeading($string, $char)
    {
        return $char . ltrim($string, $char);
    }
    
    /**
     * Returns an array from the given string.
     *
     * Multiple values in the string should be delimited by commas, and a
     * value could also be a key-value pair which are denoted by an equals 
     * sign (e.g. key1=val1,key2=val2,key3=val3).
     *
     * @param string $str a string consisting of comma-separated values or key-value pairs.
     * @return array returns an array that formed from the given string, or empty array.
     * @throws \InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public static function toArray($str) 
    {
	    if (!is_string($str)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
	    }
	    
	    // array to populate with values.
        $arr = array();	    
        if (($values = explode(',', $str)) && is_array($values)) {
            foreach ($values as $value) {
                $parts = explode('=', trim($value));
                if (is_array($parts) && count($parts) > 1) {
                    $arr[$parts[0]] = $parts[1];
                } else {
                    $arr[] = $parts[0];
                }
            }
        }
        return $arr;
    }
}
