<?php

namespace Spark\Cache\Storage;

use RecursiveDirectoryIterator;
use GlobIterator;

use Spark\Cache\Configuration\ConfigurationInterface;
use Spark\Cache\Configuration\FileConfiguration;
use Spark\Cache\Storage\File\CacheItem;
use Spark\Util\Strings;

class FileStorage extends AbstractStorage
{
    /**
     * A configuration object for the storage.
     *
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * {@inheritDoc}
     */
    protected function doGet($key, &$casToken = null)
    {
        $value = null;

        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            // open cache file.
            $item = new CacheItem($filename);
            
            if ($this->has($key)) {
                $value = unserialize($item->getCacheData());
            }
            
            // close open file pointer.
            $item = null;
        }
        
        return $value;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doHas($key)
    {
        $hasItem = false;
    
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            // open cache file.
            $item = new CacheItem($filename);
            // determine if cache item is valid.
            $hasItem = !($item->hasExpired());            
            // close open file pointer.
            $item = null;
        }
        
        return $hasItem;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doAdd($key, $value)
    {
        $hasAdded = false;
        if (!$this->has($key)) {
            $hasAdded = $this->set($key, $value);
        }
        
        return $hasAdded;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doSet($key, $value)
    {
        $config   = $this->getConfiguration();
        $filename = $this->getFileName($key);
        $path     = pathinfo($filename, \PATHINFO_DIRNAME);
        
        // create directory.
        if (!is_dir($path)) {
            mkdir($path, $config->getDirPermission(), true);
        }
        
        // define the lifetime.
        if ($config->getTimeToLive() > 0) {
            $lifetime = (time() + $config->getTimeToLive());
        } else {
            $lifetime = 0;
        }

        // file content.
        $lines = array();
        $lines[] = $lifetime;
        $lines[] = serialize($value);
        
        // create file.
        $bytesWritten = file_put_contents($filename, implode($lines, PHP_EOL));
        // change file permission.
        if ($bytesWritten !== false) {
            chmod($filename, $config->getFilePermission());
        }
        
        return ($bytesWritten !== false);
        
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doReplace($key, $value)
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            return $this->set($key, $value);
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doIncrement($key, $offset = 1, $initial = 0)
    {
        $newValue = (int) $initial;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue + $offset);
            }
            $this->set($key, $newValue);
        } else {
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDecrement($key, $offset = 1, $initial = 0)
    {
        $newValue = (int) $initial;
        if ($this->has($key)) {
            $oldValue = $this->get($key);
            if (is_numeric($oldValue)) {
                $newValue = (int) ($oldValue - $offset);
                $newValue = ($newValue < 0) ? 0 : $newValue;
            }
            $this->set($key, $newValue);
        } else {
            $this->add($key, $newValue);
        }
        
        return $newValue;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doTouch($key)
    {
        $hasTouched = false;
        if ($this->has($key)) {

            // open cache file.
            $item = new CacheItem($this->getFileName($key));
            // get unserialized value.
            $value = unserialize($item->getCacheData());
            // close open file pointer.
            $item = null;
            
            // update lifetime.
            $hasTouched = $this->set($key, $value);
        }
        
        return $hasTouched;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doDelete($key)
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            return @unlink($filename);
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doFlush()
    {
        $cacheDir = $this->getConfiguration()->getCacheDir();
        $this->flushDirectory($cacheDir);
        
        return true;
    }
    
    /**
     * Removes all files and directories that are contained within the given directory.
     * The directory reflected by the given path will be empty after this call returns.
     *
     * @param string $path the path to a directory to flush.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     * @link http://www.paulund.co.uk/php-delete-directory-and-files-in-directory
     * @link http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it#answer-3349792
     */
    private function flushDirectory($path)
    {
	    if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($path) ? get_class($path) : gettype($path))
            ));
	    }
    
        $isFlushed = true;
    
        $flags = GlobIterator::CURRENT_AS_PATHNAME | GlobIterator::SKIP_DOTS;
        $it = new GlobIterator(Strings::addTrailing($path, \DIRECTORY_SEPARATOR) . '*', $flags);
        foreach ($it as $pathName) {
            // flush all directories rescursively.
            if ($it->isDir()) {
                $this->flushDirectory($pathName);
                @rmdir($pathName);
            } else {
                @unlink($pathName);
            }
        }
    }
    

    /**
     * Delete all expired items.
     *
     * return void.
     */
    public function deleteExpired()
    {
        $now = time();
        
        $cacheDir   = $this->getConfiguration()->getCacheDir();
        $folderName = $this->toFileName($this->getConfiguration()->getPrefix());
        
        // path to a directory where expired items will be deleted.
        $path = Strings::addTrailing($cacheDir, \DIRECTORY_SEPARATOR) . $folderName . str_repeat(\DIRECTORY_SEPARATOR . '*', 4);
    
        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_FILEINFO;;
        $it = new GlobIterator(Strings::addTrailing($path, \DIRECTORY_SEPARATOR) . '*.dat', $flags);
        foreach ($it as $pathName) {
            // check each file manually, it's faster than creating CacheItems.
            if ($it->isFile()) {
                $handle = fopen($pathName, 'r');
                if ($handle !== false) {
                    $lifetime = (int) rtrim(fgets($handle), "\r\n");
                    if ($lifetime !== 0 && $now > $lifetime) {
                        // close open file pointer.
                        fclose($handle);
                        // remove cache file.
                        @unlink($pathName);
                    } else {                    
                        // close open file pointer.
                        fclose($handle);
                    }
                }
            }
        }
    }
    
    /**
     * Removes empty directories from the cache directory.
     *
     * All empty directories within the cache directory will be removed after this 
     * call returns.
     *
     * @return void
     */
    public function clean()
    {
        $cacheDir = $this->getConfiguration()->getCacheDir();
        $this->cleanDirectory($cacheDir);
    }
    
    /**
     * Cleans a directory recursively by determining if it's subdirectories are empty.
     * 
     * All empty directories within the given directory will be removed after this 
     * call returns. 
     *
     * @param string $path the path to clean.
     * @return bool true if the directory is clean and removed.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function cleanDirectory($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($path) ? get_class($path) : gettype($path))
            ));
	    }
        
        $isClean = true;
    
        $flags = GlobIterator::CURRENT_AS_PATHNAME | GlobIterator::SKIP_DOTS;
        $it = new GlobIterator(Strings::addTrailing($path, \DIRECTORY_SEPARATOR) . '*', $flags);
        foreach ($it as $pathName) {
            if ($it->isDir()) {
                // skip directory if subdirectories aren't clean.
                if ($this->cleanDirectory($pathName)) {
                    // list all files in directory.
                    $files = array_diff(scandir($pathName), array('..', '.'));
                    if (empty($files)) {
                        $isClean = (@rmdir($pathName)) ? $isClean: false;;
                    } else {
                        $isClean = false;
                    }
                }
            }
        }
        
        return $isClean;
    }
    
    /**
     * Returns an absolute path to a file.
     *
     * @param string $normalizedKey the key for which to return a filename.
     * @return string an absolute path to a file.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     * @see FileStorage::getCacheDirectory($normalizedKey); 
     */
    private function getFileName($normalizedKey)
    {
        if (!is_string($normalizedKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($normalizedKey) ? get_class($normalizedKey) : gettype($normalizedKey))
            ));
        }
        
        // append folder names to cache directory.
        $path = $this->getCacheDirectory($normalizedKey);
        // prepend if necessary extension with a dot.
        $extension = Strings::addLeading($this->getConfiguration()->getFileExtension(), '.');
        
        return $path . $normalizedKey . $extension;
    }
    
    /**
     * Returns an absolute path to a directory for the given key.
     *
     * The path is created from the given key and contains multiple directories which are formed by 
     * hashing the key and splitting the hash into 4 equal parts.
     *
     * @param string $normalizedKey the key for which to return a path.
     * @return string an absolute path to a directory for the given key.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function getCacheDirectory($normalizedKey)
    {
        if (!is_string($normalizedKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($normalizedKey) ? get_class($normalizedKey) : gettype($normalizedKey))
            ));
        }
    
        $path = Strings::addTrailing($this->getConfiguration()->getCacheDir(), \DIRECTORY_SEPARATOR);
        if (($folderName = $this->toFileName($this->getConfiguration()->getPrefix())) !== '') {
            $path .= Strings::addTrailing($folderName, \DIRECTORY_SEPARATOR);
        }
        $path .= implode(str_split(md5($normalizedKey), 8), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        
        return $path;
    }
    
    /**
     * Returns a normalized file name.
     *
     * To ensure a file name it being a folder or file is accepted by most operating systems all
     * special characters will be removed. To keep the folder structure within the cache directory
     * consisted all characters will be made lowercase. 
     *
     * @param string $name the name for which to create a file name.
     * @param int $length the maximum length of the file name.
     * @return string a normalized file name.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    private function toFileName($name, $length = 64)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
        
        $filenameSize = (int) $length;
        $filename = preg_replace('#[^a-z0-9_-]#i', '', strtolower($name));
        
        if ($filenameSize > 0 && strlen($filename) > $filenameSize) {
            $filename = substr($filename, 0, $filenameSize);    
        }
        
        return $filename;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setConfiguration($config)
    {
        if (is_array($config) || $config instanceof \Traversable) {
            $config = new Configuration($config);
        }
        
        if (!($config instanceof ConfigurationInterface)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an object that implements "ConfigurationInterface"; received "%s"',
                __METHOD__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }
    
        $this->config = $config;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        if ($this->config === null) {
            $this->setConfiguration(new FileConfiguration());
        }
        
        return $this->config;
    }
}
