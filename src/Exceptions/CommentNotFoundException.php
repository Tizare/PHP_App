<?php

namespace PHP2\App\Exceptions;

use Exception;

class CommentNotFoundException extends Exception
{
    protected $message = "Such comment not found, sorry!";
}