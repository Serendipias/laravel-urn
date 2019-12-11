<?php

namespace Serendipias\Urn\Exceptions;

use Exception;
use Throwable;

class InvalidUrnException extends Exception
{
    public function __construct($message = 'Urn is not valid', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
