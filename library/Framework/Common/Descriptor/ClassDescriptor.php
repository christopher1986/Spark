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

namespace Framework\Common\Descriptor;

use ReflectionClass;

use Framework\Cache\Storage\ArrayStorage;
use Framework\Common\Annotation\AnnotationLoader;
use Framework\Common\Annotation\AnnotationScanner;

/**
 * 
 *
 * @author Chris Harris 
 * @version 1.0.0
 */
class ClassDescriptor extends ReflectionClass
{
    /**
     * 
     */
    private $annotations;

    /**
     * A collection of annotations for this class.
     *
     * @var array
     */
    private $annotationLoader;
    
    /**
     * A flag to indicate if the class has been introspected.
     *
     * @var bool
     */
    private $hasIntrospected = false;
    
    public function getAnnotationLoader()
    {
        if ($this->annotationLoader === null) {
            $this->annotationLoader = new AnnotationLoader($this);
        }
        
        return $this->annotationLoader;
    }
    
    public function getAnnotations()
    {
        if ($this->annotations === null) {
            $scanner = new AnnotationScanner($this->getDocComment());
            $tokens = $scanner->scan();
            //var_dump($tokens);
        }
        
        return $this->annotations;
    }
}
