<?php

namespace PHP2\App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Problem with something';
}