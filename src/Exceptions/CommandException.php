<?php

namespace PHP2\App\Exceptions;
use Exception;

class CommandException extends Exception
{
    protected $message = "bad command";
}