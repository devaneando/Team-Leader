<?php

namespace AppBundle\Exception\Model;

use \Exception;

/**
 * This exception shuld be used when no category was found for the given identifier.
 */
class UnexistentCategoryException extends Exception
{
    protected $message = 'There are no categories in the database with the given identifier.';
}
