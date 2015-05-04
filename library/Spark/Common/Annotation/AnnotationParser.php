<?php

namespace Spark\Common\Annotation;

use Spark\Parser\Exception\ParserSyntaxException;
use Spark\Parser\Lexer\TokenInterface;

use SplStack;

/**
 * A recursive descent parser capable of parsing LL(k) grammars.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class AnnotationParser
{   
    /**
     * A scanner that performs the lexical analysis.
     *
     * @var Scanner
     */
    private $scanner;

    /**
     * Token types which are allowed in a class name.
     *
     * @var array
     */
    private $classIdentifiers = array(
        AnnotationLexer::T_IDENTIFIER, 
        AnnotationLexer::T_TRUE, 
        AnnotationLexer::T_FALSE, 
        AnnotationLexer::T_NULL
    );
    
    /**
     * Parse the given input string.
     *
     * @param string $input the string to parse.
     * @throws InvalidArgumentException if the given argument is not a string.
     */
    public function parse($input)
    {   
        if (!is_string($input)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        }
    
        // tokenize input string.
        $this->scanner = new AnnotationLexer($input);
        
        $annotations = array();
        while ($this->scanner->hasNext()) {
            // search for start of annotation.
            if (!$this->match($this->scanner->current(), AnnotationLexer::T_AT)) {
                $this->scanner->next();
                continue;
            }
            
            // verify that this is indeed an annotation.
            $nextToken = $this->scanner->peek();
            if ($nextToken === null || (!$this->match($nextToken, AnnotationLexer::T_NAMESPACE_SEPARATOR) && !$this->matchAny($nextToken, $this->classIdentifiers))) {
                $this->scanner->next();
                continue;
            }
            
            // parse annotation name.
            $annotations[] = $this->parseAnnotation();
 
            $this->scanner->next(); 
        }
        
        return $annotations;
    }
    
    /**
     * Returns the annotation name from a collection of tokens.
     *
     * @return string the annotation name.
     * @throws LogicException if the first token found is not a class name identifier.
     */
    private function parseAnnotation()
    {                  
        // move to next token if current does not belong to class name.
        if (!$this->matchAny($this->scanner->current(), $this->classIdentifiers)) {
            $this->scanner->next();
        }

        $className = '';
        if ($this->scanner->valid()) {
            // a class name token is expected.
            if (!$this->matchAny($this->scanner->current(), $this->classIdentifiers)) {
                throw new ParserSyntaxException(sprintf(
                    '%s: encountered an illegal character in annotation name; character: "%s"',
                    __METHOD__,
                    $this->scanner->current()->getValue()
                ));
            }
            
            // construct a class name from tokens.
            $className = $this->scanner->current()->getValue();
            // next token must be namespace separator.
            while (($token = $this->scanner->next()) !== null && $this->match($token, AnnotationLexer::T_NAMESPACE_SEPARATOR)) {                
                // only accept class name token.
                if (($token = $this->scanner->next()) !== null && $this->matchAny($token, $this->classIdentifiers)) {
                    $className .= "\\{$token->getValue()}";
                }
            }
            
            // parse parameter list.
            if ($this->match($this->scanner->current(), AnnotationLexer::T_OPEN_PARENTHESIS)) {
                // move to next token.
                $this->scanner->next();
                // parse values.
                $this->parseValues();
            }

            // move to next token.
            $this->scanner->next();
        }
        
        return $className;
    }
    
    private function parseValues()
    {
        $stack = new SplStack();

        
        
    }
    
    private function parseValue()
    {
        
    }
    
    /**
     * Returns true if the given token matches any of the token types.
     *
     * @param TokenInterface $token the token whose type will be tested.
     * @param array|Traversable $types a collection of possible token types to match.
     * @param bool $strict if true strict comparison will be performed on the token type.
     * @return bool true if the given token matches at least one token type, false otherwise.
     * @throws InvalidArgumentException if the given argument is not an array or Traversable object.
     */
    private function matchAny(TokenInterface $token, $types, $strict = true) 
    {
        if (!is_array($types) && !($types instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object as argument; received "%d"',
                __METHOD__,
                (is_object($types) ? get_class($types) : gettype($types))
            ));
        }
    
        if ($types instanceof \Traversable) {
            $types = iterator_to_array($types);
        }
    
        return in_array($token->identify(), $types, (bool) $strict);
    }
    
    /**
     * Returns true if the given token matches the given token type. 
     *
     * @param TokenInterface $token the token whose type will be tested.
     * @param mixed $type the token type to match.
     * @param bool $strict if true strict comparison will be performed on the token type.
     * @return bool true if the given token matches with the given token type, false otherwise.
     */
    private function match(TokenInterface $token, $type, $strict = true)
    {
        if ((bool) $strict) {
            return ($token->identify() === $type);
        } else {
            return ($token->identify() == $type);
        }
    }
}
