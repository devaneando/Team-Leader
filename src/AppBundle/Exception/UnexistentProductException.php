<?php

namespace AppBundle\Exception;

use \Exception;

/**
 * This exception shuld be used when no product was found for the given identifier.
 */
class UnexistentProductException extends Exception
{
    protected $message = 'There are no products in the database with the given identifier.';
}
