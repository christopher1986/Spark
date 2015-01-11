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

namespace Framework\Cache\Storage;

/**
 * An interface that allows items to be stored within a cache. By default a storage provides methods for creating, reading, 
 * updating and deleting items from the cache. Besides these four operations a storage also allows you to test the presence 
 * of an item within the cache.
 *
 * Some but not all storages provide a "check and set" operation, that will only allow you to store an item if another
 * client has not updated that item since it was last fetched. Storages that support this operation should implement the
 * {@link CheckAndSetCapableInterface} interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface StorageInterface
{    
    /** 
     * Retrieve an item.
     *
     * A CAS token will only be returned by storages that implement the {@link CheckAndSetCapableInterface} interface, 
     * otherwise the second argument if provided is simply ignored by this operation.
     *
     * @param string $key the key of the item to retrieve.
     * @param float $casToken if set will be populated with the CAS token.
     * @return mixed returns the value for the given key, or null if the item does not exist.
     */
    public function get($key, &$casToken = null);
    
    /**
     * Tests whether an item exists.
     *
     * @param string $key the key of an item whose presence will be tested.
     * @return bool true if an item exists, false otherwise.
     */
    public function has($key);
    
    /**
     * Add an item.
     *
     * @param string $key they key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    public function add($key, $value);
    
    /**
     * Store an item.
     *
     * @param string $key they key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    public function set($key, $value);
    
    /**
     * Replace the item under an existing key.
     *
     * This method is similar to {@link StorageInterface::set($key, $value)}, but the operation will
     * fail if the key does not exist.
     *
     * @param string $Key the key under which to store the value.
     * @param mixed $value the value to store.
     * @return bool true on success, false on failure.
     */
    public function replace($key, $value);
     
    /**
     * Increment a numeric item's value.
     *
     * This method will only increment numeric values. Passing anything else than a numeric value to this
     * method is considered a no-op, and will result in an error.
     *
     * @param string $key the key of the item to increment.
     * @param int $offset the amount by which to increment the item's value.
     * @param int $initial the value to set the item to if it doesn't currently exist.
     * @return mixed new item's value on succes, null on failure.
     */
    public function increment($key, $offset = 1, $initial = 0);
    
    /**
     * Decrement a numeric item's value.
     *
     * This method will only decrement numeric values. Passing anything else than a numeric value to this
     * method is considered a no-op, and will result in an error.
     *
     * @param string $key the key of the item to decrement.
     * @param int $offset the amount by which to decrement the item's value.
     * @param int $initial the value to set the item to if it doesn't currently exist.
     * @return mixed new item's value on succes, null on failure.
     */
    public function decrement($key, $offset = 1, $initial = 0);
    
    /**
     * Reset expiration of an item.
     *
     * @param string $key the key of the item whose lifetime will be reset.
     * @return bool true if the lifetime was reset, false otherwise.
     */
    public function touch($key);
    
    /**
     * Delete an item.
     *
     * @param string $key the key of the item to delete.
     * return bool true on success, false on failure.
     */
    public function delete($key);
    
    /**
     * Invalidate all items.
     *
     * Invalidates all items currently stored in the storage, after this method returns all items within
     * the storage will be removed.
     * 
     * @return bool true on success, false on failure.
     */
    public function flush();
}
