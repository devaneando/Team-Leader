<?php

namespace AppBundle\Exception\Command;

use \Exception;

/**
 * This exception shuld be used when a function or method parameter is invalid.
 */
class InvalidParameterException extends Exception
{
    protected $message = 'One of the given parameters is invalid.';
}
