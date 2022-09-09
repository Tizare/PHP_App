<?php

namespace PHP2\App\Repositories;

use PHP2\App\user\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function get(int $id): User;
}
