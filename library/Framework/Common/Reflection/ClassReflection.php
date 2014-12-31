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

namespace Framework\Common\Reflection;

use SplFileObject;
use Framework\Common\Annotation\AnnotationScanner;
use Framework\Common\Descriptor\NamespaceDescriptor;
use Framework\Scanner\PhpScanner;

class ClassReflection extends \ReflectionClass
{
    /**
     * A collection of annotations.
     *
     * @var array
     */
    private $annotations;
    
    /**
     * A file object.
     *
     * @var SplFileObject
     */
    private $file;
    
    public function getAnnotations()
    {
        $content = $this->getFileContent();
        
        $descriptor = new NamespaceDescriptor($this);
    
        if ($this->annotations === null) {
            $scanner = new AnnotationScanner($this->getDocComment());
            $this->annotations = $scanner->scan();
        }
        return $this->annotations;
    }
    
    /**
     * Returns the content of the file in which the class has been defined.
     *
     * Unlike functions such as {@link file_get_contents} that reads an entire file into a string
     * this method will read the file line-by-line. Although reading a file line-by-line is slower
     * it will most definitely use less memory in doing so.
     *
     * @param int $lineNumber the maximum number of lines to read.
     * @return string the content of the file, or an empty string if there is nothing to read.
     */
    public function getFileContent($lineNumber = -1)
    {
        $file = $this->getFile();
        
        $lineCount = 0;
        $content = '';
        while (!$file->eof()) {
            // reached the maximum number of lines to read.
            if ($lineNumber >= 0 && $lineCount++ == $lineNumber) {
                break;
            }
            
            $content .= $file->fgets();
        }
        
        return $content;
    }
    
    /**
     * Returns an object oriented interface for the file in which the class has been defined.
     *
     * @link http://php.net/manual/en/class.splfileobject.php
     */
    public function getFile()
    {
        if ($this->file === null) {
            $this->file = new SplFileObject($this->getFileName());
        }
        return $this->file;
    }
}
