<?php

namespace PHP2\App\Exceptions;

use Exception;

class AuthTokenNotFoundException extends Exception
{
        protected $message = "Token not found";
}