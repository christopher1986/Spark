<?php

namespace Framework\Net\Exception;

/**
 * Thrown to indicate that when operating on a URI or string represening an URI the operation will 
 * result in an invalid URI specification.
 *
 * @author Chris Harris <c.harris@hotmail.com>
 * @version 1.0.0
 */
class MalformedUriException extends \Exception implements \ExceptionInterface
{}
