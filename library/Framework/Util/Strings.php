<?php

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
