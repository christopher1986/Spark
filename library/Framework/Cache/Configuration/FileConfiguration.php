<?php

namespace Framework\Cache\Configuration;

use Namespace\Io\Exception\FileNotFoundException;
use Namespace\Io\Exception\AccessDeniedException;

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
    private $filePermission = 0700;
    
    /**
     * The directory permission.
     *
     * @var int
     */
    private $dirPermission = 0600;
    
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
        $allowedExtensions = array('.tag', '.dat', '.php');
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: only (%s) are valid file extensions; received "%s"',
                __METHOD__,
                implode(',' $allowedExtensions),
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
}
