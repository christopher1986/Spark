<?php

namespace Framework\Io;

use Framework\Io\Exception\IOException;

/**
 * A StringReader is capable of reading characters from a string.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class StringReader extends Reader
{
    /**
     * The characters on which the reader operates.
     *
     * @var string
     */
    protected $content;

    /**
     * The number of characters.
     *
     * @var int
     */
    protected $charCount = 0;

    /**
     * The position of the reader.
     *
     * @var int
     */
    protected $nextChar = 0;

    /**
     * The char that is marked.
     *
     * @var int
     */
    protected $markedChar = self::UNMARKED;

    /**
     * Creates a new StringReader.
     *
     * @param string|null $string the string to read.
     */
    public function __construct($string)
    {
        $this->setContent($string);
    }
    
    /**
     * Reads the given amount of the characters from the reader starting 
     * from the given offset. 
     *
     * @param int $offset the offset at which to start storing characters.
     * @param int $length the maximum number of characters to read.
     * @return string a string consisting of the characters read.
     * @throws \InvalidArgumentException if the given arguments are not integer types.
     */
    public function read($offset, $length)
    {        
        $charCount = strlen($this->content);
        if ($offset < 0 || ($offset > $charCount) || $length <= 0 ||
            (($offset + $length) > $charCount) || (($offset + $length) < 0)) {
            throw new \LogicException('The given offset or length are not valid.');
        }

        // the maximum number of characters to read.
        $length = min(($charCount - $this->nextChar), $length);
        // the start position to start reading characters from.
        $offset = min(($charCount - $length), ($this->nextChar + $offset));

        $str = substr($this->content, $offset, $length);
        if ($str === false) {
            $str = null;
        }
        
        // update position of reader.
        $this->nextChar += $length;
           
        return $str;
    }
    
    /**
     * Read the given amount of the characters from the reader.
     * 
     * @param int $amount the number of characters to read.
     * @return string|null a string containing the given amount of characters.
     */ 
    public function readChar($amount = 1)
    {
        $chars = null;
        if ($this->hasCharsLeft()) {
            $chars = $this->read(0, $amount);
        }
        
        return $chars;
    }
    
    /**
     * Read characters from the reader until one or more whitespace characters
     * are encountered.
     * 
     * @return string|null a string containing the content of the word, or null 
     *                     if the reader has reached the end of the stream.
     */    
    public function readWord()
    {        
        // move to the first non-whitespace character.
        if (($currentChar = $this->currentChar()) && is_string($currentChar)) {
            // determine if current character is whitespace.
            if ($isWhitespace = ctype_space($currentChar)) {
                // an internal mechanishm to mark a char.
                $markedChar = self::UNMARKED;
                do {
                    // mark the current character.
                    $markedChar = $this->nextChar;
                    // read the next character.
                    $currentChar = $this->readChar();
                    // determine if next character is whitespace.
                    $isWhitespace = ctype_space($currentChar);
                } while ($isWhitespace);

                // reset reader to marked char.
                $this->nextChar = $markedChar;
            }
        }
        
        $word = null;
        if ($this->hasCharsLeft()) {        
            if (preg_match('#(.[^\s]*)#', $this->content, $matches, 0, $this->nextChar)) {
                // get matched word.
                $word = $matches[1];
                // update char count.
                $this->nextChar += strlen($word);
            }
        }
        
        return $word;
    }
    
    /**
     * Read characters from the reader until a line termination character
     * is encountered.
     * 
     * @return string|null a string containing the content of the a line, or null 
     *                     if the reader has reached the end of the stream.
     */
    public function readLine()
    {
        $line = null;
        if ($this->hasCharsLeft()) {        
            if (preg_match('#(.[^\n]*)#', $this->content, $matches, 0, $this->nextChar)) {
                // get matched line.
                $line = $matches[1];
                // update char count.
                $this->nextChar += strlen($line);
            }
        }
        
        return $line;
    }  
    
    /**
     * Mark the current position of the reader. Calling reset() will
     * reset the reader postion to the marked position.
     *
     */
    public function mark()
    {
        $this->markedChar = $this->nextChar; 
        
    }
    
    /**
     * Reset the reader to the most recent mark.
     *
     * @throws \LogicException if no has mark has been set.
     */
    public function reset()
    {
        if ($this->markedChar < 0) {
            throw new \LogicException(sprintf(
                'No mark has been set, call %s::mark() first.',
                __Class__
            ));
        }
        
        // update next char position.
        $this->nextChar = $this->markedChar;
        // reset marker again.
        $this->markedChar = self::UNMARKED;
    }
    
    /**
     * Skip the given number of characters.
     *
     * @param int $amount the number of characters to skip.
     * @see StringReader::read($offset, $length)
     */
    public function skip($amount = 1)
    {
        if (!is_numeric($amount) || $amount < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a positive numeric argument; received "%s"',
                __METHOD__,
                (is_object($amount) ? get_class($amount) : gettype($amount))
            ));
        }
        
        $this->read(0, (int) $amount);
    }
    
    /**
     * {@inheritDoc}
     */
    public function peek($amount = 1)
    {
        $chars = '';
    
        $this->mark();
        $chars = $this->read(0, (int) $amount);
        $this->reset();
        
        return $chars;
    }
    
    /**
     * Set the content on which the reader will operate.
     *
     * @param string $content a string containing content.
     */
    protected function setContent($content)
    {
        if (!is_string($content)) {
            throw new IOException(sprintf(
                '%s: expects a string as argument; received "%s"',
                __METHOD__,
                (is_object($content) ? get_class($content) : gettype($content))
            ));
        }
        
        $this->content = $content;
        $this->charCount = strlen($content);
    }

    /**
     * Returns the position of the reader within the stream.
     *
     * @return int the position within the stream.
     */
    public function getPosition()
    {
        return $this->nextChar;
    }

    /**
     * Determine whether the reader has reached the end of the sequence of characters.
     *
     * @return bool true if there are still characters left, false otherwise.
     */
    protected function hasCharsLeft()
    {
        return ($this->nextChar < $this->charCount);
    }
    
    /**
     * Returns the current character within the reader.
     *
     * @return string the current character.
     */
    protected function currentChar()
    {
        $char = null;
        if (isset($this->content[$this->nextChar])) {
            $char = $this->content[$this->nextChar];
        }
        
        return $char;
    }
}
