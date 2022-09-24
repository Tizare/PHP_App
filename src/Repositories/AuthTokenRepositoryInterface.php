<?php

namespace PHP2\App\Repositories;

use PHP2\App\user\AuthToken;

interface AuthTokenRepositoryInterface
{
    public function get(string $token): AuthToken;
}