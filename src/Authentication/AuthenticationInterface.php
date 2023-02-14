<?php

namespace PHP2\App\Authentication;

use PHP2\App\Request\Request;
use PHP2\App\user\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}