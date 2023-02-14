<?php

namespace PHP2\App\Exceptions;

use Exception;

class AuthException extends Exception
{
    protected $message = 'Problem with Auth';
}