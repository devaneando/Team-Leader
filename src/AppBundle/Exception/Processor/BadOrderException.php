<?php

namespace AppBundle\Exception\Processor;

use \Exception;

/**
 * This exception shuld be used when an order is null or invalid.
 */
class BadOrderException extends Exception
{
    protected $message = 'The given order is not acceptable.';
}
