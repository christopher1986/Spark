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

use Framework\Common\Comparator\ComparatorInterface;

/**
 * This class contains various methods for normalizing and testing arrays.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class Arrays
{
    /**
     * Returns a normalized array by making each key in the given array lowercase.
     *
     * @param array|\Traversable $arr the array or collection object to normalize.
     * @return array returns a normalized array where each key is lowercase.
     */
    public static function normalize($arr)
    {
        if (!is_array($arr) && !($arr instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($arr) ? get_class($arr) : gettype($arr))
            ));
        }
        
        // new array to hold normalized keys and values.
        $newArr = array();
        foreach ($arr as $key => $value) {
            // normalize key by making it lowercase.
            $normalizedKey = strtolower($key);
            // add normalized key and value to a new array.
            $newArr[$normalizedKey] = $value;
        }
        
        return $newArr;
    }
    
    /**
     * Returns true if the given array is considered to be associative, false otherwise.
     *
     * @param array $arr the array to check.
     * @return bool returns true if the given array is associative, false otherwise.
     * @link http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential#answer-4254008
     */
    public static function isAssoc($arr)
    {
        return (bool) count(array_filter(array_keys($arr), 'is_string'));
    }
    
    /**
     * Returns true if the given array is considered to be multidimensional, false otherwise.
     *
     * @param array $arr the array to check.
     * @return bool returns true if the given array is multidimensional, false otherwise.
     * @link http://pageconfig.com/post/checking-multidimensional-arrays-in-php
     */
    public static function isMultiArray(array $arr) 
    {
        rsort($arr);
        return isset($arr[0]) && is_array($arr[0]);
    }
    
    /**
     * Converts an iterator object to an array.
     *
     * @param array|Traversable $iterator the iterator to convert.
     * @param bool $recursive apply conversion recursively.
     * @return array returns a new array with the keys/value pairs
     *               of the iterator.
     * @link https://github.com/zendframework/zf2/blob/master/library/Zend/Stdlib/ArrayUtils.php#L204
     */
    public static function iteratorToArray($iterator, $recursive = true) 
    {
        if (!is_array($iterator) && !($iterator instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($iterator) ? get_class($iterator) : gettype($iterator))
            ));
        }

        if (!((bool) $recursive)) {
            if (is_array($iterator)) {
                return $iterator;
            }

            return iterator_to_array($iterator);
        }

        if (method_exists($iterator, 'toArray')) {
            return $iterator->toArray();
        }

        $array = array();
        foreach ($iterator as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }

            if ($value instanceof \Traversable) {
                $array[$key] = $this->iteratorToArray($value, $recursive);
                continue;
            }

            if (is_array($value)) {
                $array[$key] = $this->iteratorToArray($value, $recursive);
                continue;
            }

            $array[$key] = $value;
        }

        return $array;
    }
    
    /**
     * Accepts a variable number of arrays where the key-value pairs of each array 
     * is added to it's sibling array.
     *
     * @return array the resulting array after combining all the given arrays.
     */
    public static function addAll()
    {
        // the resulting array.
        $array = array();    
        
        // a variable-length argument list.
        if ($args = func_get_args()) {
            // merge arguments that are of type array.
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    foreach (array_keys($arg) as $key) {
                        if (isset($array[$key]) && is_array($array[$key]) && is_array($arg[$key])) {
                            // recursively copy values from multidimensional arrays.
                            $array[$key] = self::addAll($array[$key], $arg[$key]); 
                        } else {
                            // copy value to resulting array.
                            $array[$key] = $arg[$key]; 
                        }
                    }
                }
            }
        }

        return $array;
    }
    
    /**
     * Returns a normalized key.
     *
     * @param string $key the key to normalize.
     * @return string a normalized key.
     */
    public static function normalizeKey($key)
    {
        $key = str_replace(array(' ', '-', '\\'), '_', strtolower($key));
        return preg_replace('/[^A-Za-z0-9_]/', '', $key);
    }
    
    /**
     * Sorts the specified array according to a comparator.
     *
     * @param array $arr the array to be sorted.
     * @param ComparatorInterface $comparator the comparator that will determine the order of the array.
     */
    public static function sort(array &$arr, ComparatorInterface $comparator)
    {    
        return usort($arr, array($comparator, 'compare'));
    }
}
