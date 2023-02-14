<?php

namespace PHP2\App\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $message = "Bad connection";
}