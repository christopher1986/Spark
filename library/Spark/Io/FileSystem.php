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

namespace Spark\Io;

use Spark\Util\Strings;

/**
 * A class that provides an object oriented interface for file system operations.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 0.0.3
 */
class FileSystem
{
    /**
     * Pathname to either a directory or file.
     *
     * @var string
     */
    private $pathname;

    /**
     * Constructor
     *
     * Creates a new File instance from a parent pathname and will append an optional child pathname to the parent pathname
     * if the parent is not a file.
     *
     * @param string|File $parent a pathname string or File object.
     * @param string|null $child (optional) child pathname string.
     */
    public function __construct($parent, $child = null)
    {                
        $path = ($parent instanceof File) ? $parent->getPath() : (string) $parent;        
        if(!is_file($path) && is_string($child)) {
            $path = Strings::addTrailing($path, '/') . ltrim($child, '/');
        }
        $this->pathname = $path;
    }

    /**
     * Tests if this file or directory exists and is readable.
     *
     * @return bool true if and only if the file or directory exists and is readable.
     */
    public function canRead()
    {
        return is_readable($this->pathname);
    }
    
    /**
     * Tests if this file or directory exists and is writable.
     *
     * @return bool true if and only if the file or directory exists and is writable.
     */
    public function canWrite()
    {      
        return is_writable($this->pathname);
    }
    
    /**
     * Deletes the file or directory denoted by the pathname.
     *
     * @return bool true if and only if the file or directory is successfully deleted; false otherwise.
     */
    public function delete()
    {
        if($this->exists()) {
            return unlink($this->pathname);
        }
        return false;
    }
    
    /**
     * Tests whether the file or directory denoted by the pathname exists.
     *
     * @return bool true if and only if the file or directory denoted by the path exists; false otherwise
     */
    public function exists()
    {
        return file_exists($this->pathname);
    }
    
    /**
     * Returns the name of the file or absolute directory denoted by the pathname.
     * 
     * @return string returns the name of the file or absolute directory denoted by the path.
     */
    public function getName()
    {
        if($this->hasExtension()) {
            return preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->pathname));
        }
        return $this->pathname;
    }
    
    /**
     * Returns the absolute path to a file or directory denoted by the pathname.
     *
     * @return string returns the absolute path to a file or directory denoted by the path.
     */
    public function getPath()
    {
        if($this->hasExtension()) {
            return dirname($this->pathname);
        }
        return $this->pathname;
    }
    
    /**
     * Returns the absolute pathname.
     *
     * @return string returns the absolute pathname, or empty string if the file does not exist.
     */
    public function getAbsolutePath()
    {
        $pathname = $this->normalizePath($this->pathname);
        return (file_exists($pathname)) ? $pathname : '';
    }
    
    /**
     * Returns the canonical pathname.
     *
     * @return string returns the canonicalized absolute pathname, or empty string if the file does not exist.
     */
    public function getCanonicalPath()
    {
        $pathname = realpath($this->pathname);
        return (is_string($pathname)) ? $pathname : '';
    }
    
    /**
     * Returns the time that the file or directory denoted by the pathname was last modified.
     *
     * @return int a unix timestamp, or -1 if the file or directory does not exist.
     */
    public function lastModified()
    {
        if($this->exists()) {
            $modifiedTime = filemtime($this->pathname);
            if($modifiedTime) {
                return $modifiedTime;
            }
        }
        return -1;
    }
    
    /**
     * Returns true if the pathname has a file extension.
     *
     * @returns bool true if and only if the pathname ends with a file extensions; false otherwise.
     */
    public function hasExtension()
    {
        if ($extension = $this->getExtension()) {
            return (strlen(trim($extension)) > 0);
        }
        return false;
    }
    
    /**
     * Returns if present the file extension.
     *
     * @return string the file extension.
     */
    public function getExtension()
    {
        return pathinfo($this->pathname, PATHINFO_EXTENSION);
    }
    
    /**
     * Creates the directory denoted by the pathname, and allows the creation of nested directories specified in the pathname.
     *
     * @param int $mode The mode is 0777 by default, which means the widest possible access.
     * @param bool $recursive Allows the creation of nested directories specified in the pathname.
     * @return bool true if and only if the directory was created, along with all necessary parent directories; false otherwise
     */
    public function mkdir($mode = 0777, $recursive = false)
    {   
        return mkdir($this->pathname, $mode, $recursive);
    }
    
    /**
     * Creates a file with a unique filename, with access permission set to 0600, in the directory denoted by the pathname.
     *
     * @param string $prefix the prefix of the generated temporary filename.
     * @return returns the new temporary filename, or false on failure.
     */
    public function createTempFile($prefix = null)
    {
        $prefix = (!empty($prefix)) ? (string) $prefix : $this->generateName();
        return tempnam($this->getPath(), $prefix);
    }
    
    /**
     * Normalizes the given path by recursively removing './' or '../' from the beginning of the path.
     *
     * @param string $pathname the path that should be resolved.
     * @param string $directory the directory that will be prepended to the given path.
     * @return string returns a normalized path that for example can be used to create an absolute path.
     */
    private function normalizePath($pathname, $directory = null)
    {
        if (!is_string($directory)) {
            $directory = getcwd();
        }
                
        if(Strings::startsWith($pathname, '../')) {
            $pos = strpos($pathname, '/');
            if($pos !== false) {
                return $this->normalizePath(substr($pathname, ($pos + 1)), dirname($directory));
            } 
        } else if(Strings::startsWith($pathname, './')) {
            $pos = strpos($pathname, '/');
            if($pos !== false) {
                return $this->normalizePath(substr($pathname, ($pos + 1)), $directory);
            } 
        }        
        return Strings::addTrailing($directory, '/') . $pathname;
    }
    
    /**
     * Generates a random filename with the given length.
     *
     * @param int $length the number of characters the filename should contain, defaults to 10.
     * @return string returns a unique filename of the given length.
     */
    private function generateName($length = 10)
    {
        $length = (is_numeric($length)) ? (int) $length : 10;
        $allowedChars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $filename = '';
        for($i = 0; $i < $length; $i++) {
            $random = mt_rand(0, (strlen($allowedChars) - 1));
            $filename .= $allowedChars[$random];
        }
        return $filename;
    }
}
