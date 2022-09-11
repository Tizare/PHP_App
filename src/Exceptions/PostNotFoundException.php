<?php

namespace PHP2\App\Exceptions;

use Exception;

class PostNotFoundException extends Exception
{
    protected $message = "Such post not found";
}
