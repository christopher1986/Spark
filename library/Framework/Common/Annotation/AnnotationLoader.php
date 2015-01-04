<?php

namespace Framework\Common\Annotation;


use Framework\Cache\ArrayStorage;
use Framework\Common\Annotation\Resolver\ResolverInterface;
use Framework\Common\Annotation\Resolver\AnnotationResolver;
use Framework\Common\Descriptor\ClassDescriptor;
use Framework\Util\Strings;

class AnnotationLoader
{    
    /**
     * A cache to optimize the loader.
     *
     * @var ArrayStorage
     */
    private static $cache;

    /**
     * A collection of use statements.
     *
     * @var array
     */
    private $uses;

    /**
     * An object that describes the class for which the loader was instantiated.
     *
     * @var ClassDescriptor
     */
    private $classDescriptor;
    
    /**
     * Creates a new annotation loader.
     *
     * @param string|ClassDescriptor a class to describe or a ClassDescriptor.
     */
    public function __construct($class)
    {
        $this->setClassDescriptor($class); 
    }
    
    public function getAnnotation($annotationName)
    {
        $annotation = $this->resolve($annotationName);
        return $annotation;   
    }
    
    /**
     * Set class for which to load annotations.
     *
     * @param mixed a class to describe or a ClassDescriptor.
     */
    private function setClassDescriptor($class)
    {        
        if (is_string($class) || (is_object($class) && !($class instanceof ClassDescriptor))) {
            $class = new ClassDescriptor($class);
        }

        if (!($class instanceof ClassDescriptor)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a class name or ClassDescriptor as argument; received "%s"',
                __METHOD__,
                (is_object($class) ? get_class($class) : gettype($class))
            ));
        }
        
        $this->classDescriptor = $class;
    }
    
    /**
     * Returns the class for which the loader was instantiated.
     *
     * @return ClassDescriptor object that describes the class.
     */
    private function getClassDescriptor()
    {
        return $this->classDescriptor;
    }
    
    /**
     * Returns a multidimensional array consisting of use statements.
     *
     * Will attempt to load the use statements from a cache, otherwise the namespace of the class will
     * be introspected and any statement found during that introspection will be stored in the cache.
     *
     * @return array a multidimensional array of use statements
     * @see NamespaceDescriptor::getUseStatements()
     */
    private function getUseStatements()
    {    
        $uid = $this->getClassUID();
        
        $info = ($this->getCache()->has($uid)) ? $this->getCache()->get($uid) : array();
        if (isset($info['uses']) && is_array($info['uses'])) {
            return $info['uses'];
        }
        
        if ($statements = $this->getClassDescriptor()->getUseStatements()) {
            foreach ($statements as $statement) {
                $use = (isset($statement['use'])) ? trim((string) $statement['use'], '\\') : '';
                $as  = (isset($statement['as']) && $statement['as'] !== null) ? $statement['as'] : $use;
                
                $info['uses'][$use] = $as;
            }
            $this->getCache()->add($uid, $info);
        }
        
        return $info['uses'];
    }
    
    private function getNamespace()
    {
        $uid = $this->getClassUID();
        
        $info = array();
        if ($info = ($this->getCache()->get($uid)) !== null) {
            if (isset($info['namespace']) && is_string($info['namespace'])) {
                return $info['namespace'];
            } 
        }

        $info['namespace'] = $this->getClassDescriptor()->getNamespaceName();
        $this->getCache()->add($uid, $info);

        return $info['namespace'];
    }
    
    /**
     * Returns a caching object.
     *
     * @return ArrayStorage a storage object.
     */
    private function getCache()
    {
        if (self::$cache === null) {
            self::$cache = new ArrayStorage();
        }
        return self::$cache;
    }
    
    /**
     * Returns a hash id for the class whose annotations to load.
     *
     * @return string a hash id.
     */
    private function getClassUID()
    {
        $classDescriptor = $this->getClassDescriptor();
        $filename = ($classDescriptor->getFileName() !== false) ? $descriptor->getFileName() : '';
        
        return md5(sprintf('%s::%s', $filename, $classDescriptor->getShortName()));
    }
    
    /**
     * Resolves the given annotation into a fully qualified name.
     *
     * @param string $annotationName the name to resolve.
     * @return string a fully qualified name.
     * @throws InvalidArgumentException if the given argument is not of type string.
     */
    private function resolve($annotationName)
    {        
	    if (!is_string($annotationName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($annotationName) ? get_class($annotationName) : gettype($annotationName))
            ));
	    }
    
        // annotation already fully qualified.
        if (strpos($annotationName, '\\') !== 0) {
            return $annotationName;
        }
        
        $uses = $this->getUseStatements();        
        if (!empty($uses)) {
            $alias = $annotationName;
            if (($pos = strpos($annotationName, '\\')) !== false) {
                $alias = substr($annotationName, 0, $pos);
            }
            
            if (($use = array_search($alias, $uses)) !== false) {
                $annotationName = substr($annotationName, strlen($alias));
                if (strlen($annotationName) > 0) {
                    return $use . Strings::addLeading($annotationName, '\\');
                } else {
                    return $use;
                }
            }
        }
        
        return $annotationName;
    }
}
