<?php

namespace PHP2\App\Repositories;

use PHP2\App\user\User;

interface UserRepositoryInterface
{
    public function get(int $id): User;
    public function getUserByUsername(string $username): User;
}
