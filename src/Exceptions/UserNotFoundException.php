<?php

namespace PHP2\App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'Such user not found';
}