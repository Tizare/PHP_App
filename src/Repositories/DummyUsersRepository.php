<?php

namespace PHP2\App\Repositories;

use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\user\User;

class DummyUsersRepository implements UserRepositoryInterface
{

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        throw new UserNotFoundException("User with id - 777 not found" . PHP_EOL);
    }

    public function getUserByUsername(string $username): User
    {
        return new User('username', 'name', 'surname');
    }
}