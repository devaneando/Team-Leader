<?php

namespace AppBundle\Exception\Model;

use \Exception;

/**
 * This exception shuld be used when a product price is invalid.
 */
class InvalidProductPriceException extends Exception
{
    protected $message = 'The given product price is invalid.';
}
