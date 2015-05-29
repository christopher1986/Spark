<?php
/**
 * Copyright (c) 2015, Chris Harris.
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
 * @copyright  Copyright (c) 2015 Chris Harris
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Spark\Db\Adapter\Driver\Wpdb;

class Parameter implements ParameterInterface
{
    /**
     * Indicates a string value.
     *
     * @var int
     */
    const PARAM_STR = 0x01;
    
    /**
     * Indicates an integer value.
     *
     * @var int
     */
    const PARAM_INT = 0x02;
    
    /**
     * Indicates a float value.
     *
     * @var int
     */
    const PARAM_FLOAT = 0x04;

    /**
     * The parameter name.
     *
     * @var string
     */
    private $name;
    
    /**
     * The parameter value.
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * The data type for the parameter value.
     *
     * @var int a valid parameter type.
     */
    private $type;
    
    /**
     * Create a new parameter.
     *
     * @param string the name of the parameter.
     * @param mixed $value the value to replace the parameter with.
     * @param 
     */
    public function __construct($name, $value = '', $type = self::PARAM_STR)
    {
        $this->setName($name);
        $this->setType($type);
        $this->setValue($value);
    }
    
    /**
     * {@inheritDoc}
     */
    private function setName($name)
    {        
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
    
        $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set data type of the value.
     *
     * @param int $type the data type.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {        
        return $this->convertValue($this->value, $this->getType());
    }
    
    /**
     * Set data type of the value.
     *
     * @param int $type the data type.
     */
    public function setType($type)
    {
        $allowed = array(
            self::PARAM_STR, 
            self::PARAM_INT, 
            self::PARAM_FLOAT,
        );
        
        $this->type = (in_array($type, $allowed)) ? $type : self::PARAM_STR;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Reset parameter to it's original state.
     */
    public function reset()
    {
        $this->value = '';
        $this->type  = self::PARAM_STR;
    }
    
    /**
     * Returns a value of the appropriate type.
     *
     * Since there no guarantee that the value can actually be cast typed into the given type it is 
     * the programmer's responsibility to ensure that the given value does match with given type, 
     * failing to do so may result in unexpected behaviour such as ending up with wrong values.
     *
     * @param mixed $value the value that will be converted.
     * @param int $type the type into which the value will be converted.
     * @return mixed the converted value.
     * @link http://stackoverflow.com/questions/833510/php-pdobindparam-data-types-how-does-it-work#answer-865979
     */
    private function convertValue($value, $type = self::PARAM_STR)
    {
        if (is_array($value)) {
            $values = $value;
            foreach ($values as $key => $value) {
                $values[$key] = $this->convertValue($value, $type);
            }
            return $values;
        }
        
        if ($type === self::PARAM_INT) {
            $value = $this->convertToInt($value);
        } else if ($type === self::PARAM_FLOAT) {
            $value = $this->convertToFloat($value);
        } else {
            $value = $this->convertToString($value);
        }
        
        return $value;
    }
    
    /**
     * Returns a string representation of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method.
     * Other values such as resources or objects that have not string representation will be 
     * will be ignored which will result in an empty string being returned.
     *
     * @param mixed $value the value to convert.
     * @return string the value converted to a string type.
     */
    private function convertToString($value)
    {
        $retval = '';
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $retval = (string) $value;
        }
        
        return $retval;
    }
    
    /**
     * Returns an integer value of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method.
     * Other values such as resources or objects that have not string representation will be 
     * ignored which will result in a zero integer value (0) being returned.
     *
     * @param mixed $value the value to convert.
     * @return int the value converted to an integer type.
     */
    private function convertToInt($value)
    {
        $retval = 0;
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            // intermediate step that will cast an object into a string.
            if (is_object($value)) {
                $value = (string) $value;
            }
            
            // cast to integer.
            $retval = (int) $value;
        }
        
        return $retval;
    }
    
    /**
     * Returns a floating point value of the given value.
     * 
     * The given value can be any scalar value or object that implements the __toString() method. 
     * Other values such as resources or objects that have not string representation will be 
     * ignored which will result in a zero floating value (0.0) being returned.
     *
     * @param mixed $value the value to convert.
     * @return float the value converted to a float type.
     */
    private function convertToFloat($value)
    {
        $retval = 0.0;
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            // intermediate step that will cast an object into a string.
            if (is_object($value)) {
                $value = (string) $value;
            }
            
            // cast to float.
            $retval = (float) $value;
        }
        
        return $retval;
    }
}
