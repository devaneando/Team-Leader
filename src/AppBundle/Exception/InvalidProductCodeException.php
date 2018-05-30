<?php

namespace AppBundle\Exception;

use \Exception;

/**
 * This exception shuld be used when a product code is invalid.
 */
class InvalidProductCodeException extends Exception
{
    protected $message = 'The given product code is invalid.';
}
