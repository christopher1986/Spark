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

namespace Framework\Cache\Configuration;

/**
 * The ConfigurationInterface defines how a storage should read and write items to the cache.
 *
 * Some storages are configurable, but do not make full use of a configuration object. For example a lightweight 
 * storage might not have time to live capabilities or support for a key pattern. However a configuration object
 * should still implement all these methods for a storage object that may require it. 
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface ConfigurationInterface
{
    /**
     * Determines if the cache is readable.
     *
     * @return bool true if the cache is readable, false otherwise.
     */
    public function isReadable();
    
    /**
     * Determines if the cache is writable.
     *
     * @return bool true if the cache is writable, false otherwise.
     */
    public function isWritable();
    
    /**
     * Returns a pattern that a key must match.
     *
     * @return string a pattern to match a key aganst.
     */
    public function getKeyPattern();
    
    /**
     * Returns the expiration time in seconds before an item is invalidated.
     *
     * A time to live of 0 will result in an item that never expires, although some storages
     * may still delete an item to make place for other items.
     *
     * @return int time in seconds an item should remain in the cache.
     */
    public function getTimeToLive();
    
    /**
     * Returns a string that will be prepended to the key of an item.
     *
     * @return string a string that will be prepended to a key.
     */
    public function getPrefix();
    
    /**
     * Return an array containing all the properties of this configuration.
     *
     * @return array an array representation of this configuration.
     */
    public function toArray();
}
