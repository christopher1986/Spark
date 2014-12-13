<?php

namespace Framework\Collection;

/**
 * An object that maps keys to values. A key can only be mapped to a single value, and it's for that reason impossible
 * to store duplicate keys. A map is similar to a dictionary or lookup table available in other programming languages.
 * 
 * Within the PHP programming language the Map is similar to an associative array which is also capable of mapping keys to values.
 * When performance is an issue one should always choose an (associative) array over a map implementation since native functionality 
 * is written in C++ and those functionalities will always outperform any class written in PHP. This however does not mean one should 
 * avoid a Map object since it consists of many useful functionalities that otherwise would have to be written by the developer to achieve
 * the same functionality using an (associative) array. So in other words you should use a map when you require some or all of it's 
 * functionalities and one can always diverge to an (associative) array if performance becomes an issue.
 * 
 * @author Chris Harris
 * @version 1.0.0.
 */
interface MapInterface extends \Iterator, \Countable
{
    /**
     * Associate the specified value with the specified key in this map. If a value was already associated with the given
     * key it's mapping to the key will be replaced by the new value.
     *
     * @param int $key the key to be mapped with the given value.
     * @param mixed $value the value to add to this map.
     * @return mixed the previously associated value with the key, or null if this key had no mapping to a value.
     * @throws InvalidArgumentException if the given key is null.
     */
    public function put($key, $value);
    
    /**
     * Add to this map all of the mappings that are contained in the specified map.
     *
     * @param Map $map copies all the mappings from the specified map into this one.
     * @throws \InvalidArgumentException if the given map is null.
     */
    public function putAll(MapInterface $map);
    
    /**
     * Removes all mappings from this map. The map will be empty after this call returns.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Returns true if this map contains the specified key. More formally returns true only if this map
     * contains a value $k such that ($k === $key).
     *
     * @param mixed $key the key whose presence will be tested.
     * @return bool true if this map contains the specified key, false otherwise.
     */
    public function containsKey($key);
    
    /**
     * Returns true if this map contains the specified value. More formally returns true only if this map
     * contains a value $v such that ($v === $value).
     *
     * @param mixed $value the value whose presence will be tested.
     * @return bool true if this map contains the specified value, false otherwise.
     */
    public function containsValue($value);
    
    /**
     * Returns the value that is mapped to the specified key.
     *
     * @param mixed $key key to which zero or more values are mapped.
     * @return mixed the value associated with the given key, or null.
     */
    public function get($key);

    /**
     * Returns true if this map is considered to be empty.
     *
     * @return bool true is this map contains no values, false otherwise.
     */
    public function isEmpty();
    
    /**
     * Removes the specified value from this map if it is present. More formally removes a value $v
     * such that ($v === $value), if this map contains such a value.
     *
     * @param mixed $key the value to remove from this map.
     * @return mixed the value that was removed from the map, or null if the value was not found.
     */
    public function remove($key);
    
    /**
     * Returns a set with all the keys that are contained by this map.
     *
     * @return SetInterFace a set with all the keys that are contained by this map.
     */
    public function keySet();
    
    /**
     * Returns a list with all the values that are contained by this map.
     *
     * @return ListInterface a list with the value that are contained by this map.
     */
    public function values();
    
    /**
     * Returns an array containing all values in this map. The caller is free to modify the returned
     * array since it has no reference to the actual values contained by this map.
     *
     * @return array an array containing all values from this map.
     */
    public function toArray();
}
