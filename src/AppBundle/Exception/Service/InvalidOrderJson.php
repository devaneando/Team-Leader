<?php

namespace AppBundle\Exception\Service;

use \Exception;

/**
 * This exception shuld be used when a json is invalid for the class it should represent..
 */
class InvalidOrderJson extends Exception
{
    protected $message = 'The given json was invalid or has non correspondent objects.';
}
