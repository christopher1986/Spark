<?php

namespace Framework\Cache\Configuration;

use Framework\Io\Exception\FileNotFoundException;
use Framework\Io\Exception\AccessDeniedException;

class FileConfiguration extends Configuration
{
    /**
     * The cache directory.
     *
     * @var string
     */
    private $cacheDir;
    
    /**
     * The file extension.
     *
     * @var string
     */
    private $fileExtension = '.dat';
    
    /**
     * The file permission.
     *
     * @var int
     */
    private $filePermission = 0600;
    
    /**
     * The directory permission.
     *
     * @var int
     */
    private $dirPermission = 0700;
    
    /**
     * Set the cache directory.
     *
     * @param string|null $path path to the cache directory.
     * @throws FileNotFoundException if the given path is not a directory.
     * @throws AccessDeniedException if the given directory is not readable.
     * @throws AccessDeniedException if the given directory is not writable.
     */
    public function setCacheDir($path = null)
    {
        if (is_string($path) && strlen($path) > 0) {       
            if (!is_dir($path)) {
                throw new FileNotFoundException($path, 'path is not a directory');
            } else if (!is_readable($path)) {
                throw new AccessDeniedException($path, 'directory is not readable');
            } else if (!is_writable($path)) {
                throw new AccessDeniedException($path, 'directory is not writable');
            }
        } else {
            $path = sys_get_temp_dir();
        }
        
        $this->cacheDir = rtrim($path, \DIRECTORY_SEPARATOR);
    }
    
    /**
     * Returns the cache directory.
     *
     * @return string the cache directory.
     */
    public function getCacheDir()
    {
        // use temporary files path as fallback.
        if ($this->cacheDir === null) {
            $this->setCacheDir(null);
        }
    
        return $this->cacheDir;
    }
    
    /**
     * Set the extension to use for cache files.
     *
     * @param string $extension the file extension.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     * @throws InvalidArgumentException if the extension given is not allowed.
     */
    public function setFileExtension($extension)
    {
        if (!is_string($extension)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($extension) ? get_class($extension) : gettype($extension))
            ));
        }
        
        $fileExtension = Strings::addLeading(strtolower($extension), '.');
        $allowedExtensions = array('.dat', '.php');
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: only (%s) are valid file extensions; received "%s"',
                __METHOD__,
                implode(',', $allowedExtensions),
                (is_object($fileExtension) ? get_class($fileExtension) : gettype($fileExtension))
            ));
        }
    
        $this->fileExtension = $fileExtension;
    }
    
    /**
     * Returns the file extension to use for cache files.
     *
     * @return string the file extension.
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }
    
    /**
     * Set the permission for a cache file.
     *
     * @param string|int file permission represented as a octal value.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     * @throws RuntimeException if the given permission does not meet the minimum permission.
     */
    public function setFilePermission($mode)
    {
        if (!is_numeric($mode)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($mode) ? get_class($mode) : gettype($mode))
            ));
        }
        
        if (($mode & 0600) !== 0600) {
            throw new \RuntimeException(sprintf(
                '%s: owner must be able to execute, read and write to file; permission should be 0600.',
                __METHOD__
            ));
        }
        
        $this->filePermission = $mode;
    }
    
    /**
     * Returns the permission for a cache files.
     *
     * @return file permission.
     */
    public function getFilePermission()
    {
        return $this->filePermission;
    }
    
    /**
     * Set the permission for a cache directory.
     *
     * @param string|int file permission represented as a octal value.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     * @throws RuntimeException if the given permission does not meet the minimum permission.
     */
    public function setDirPermission($mode)
    {
        if (!is_numeric($mode)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($mode) ? get_class($mode) : gettype($mode))
            ));
        }
        
        if (($mode & 0700) !== 0700) {
            throw new \RuntimeException(sprintf(
                '%s: owner must be able to read and write to directory; permission should be 0700.',
                __METHOD__
            ));
        }
        
        $this->dirPermission = $mode;
    }
    
    /**
     * Returns the permission for a cache directory.
     *
     * @return directory permission.
     */
    public function getDirPermission()
    {
        return $this->dirPermission;
    }
}
