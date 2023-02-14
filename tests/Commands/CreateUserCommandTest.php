<?php

namespace Test\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\DummyUsersRepository;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\user\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with username - Ivan is already exist");

        $command->handle(new Argument([
                'username' => 'Ivan',
                'name' => 'name',
                'surname' => 'surname'
            ]));
    }

    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {

            public function get(int $id): User
            {
                // TODO: Implement get() method.
            }

            public function getUserByUsername(string $username): User
            {
                throw new UserNotFoundException();
            }
        };
    }

    public function testItRequiresUsername(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - username");

        $command->handle(new Argument([
            'user' => 'Ivan',
            'name' => 'name',
            'surname' => 'surname'
        ]));
    }

    public function testItRequiresName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - name");

        $command->handle(new Argument([
            'username' => 'Ivan',
            'surname' => 'surname'
        ]));
    }

    public function testItRequiresSurname(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - surname");

        $command->handle(new Argument([
            'name' => 'name',
            'username' => 'Ivan',
        ]));
    }

}