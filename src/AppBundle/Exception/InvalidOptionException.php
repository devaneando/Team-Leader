<?php

namespace AppBundle\Exception;

use \Exception;

/**
 * This exception shuld be used when a command parameter is invalid or missing.
 */
class InvalidOptionException extends Exception
{
    protected $message = 'One of the given options is invalid or missing.';
}
