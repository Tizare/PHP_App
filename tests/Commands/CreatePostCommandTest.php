<?php

namespace Test\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Repositories\DummyUsersRepository;
use PHPUnit\Framework\TestCase;

class CreatePostCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserIdNotExist(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 not found");

        $command->handle(new Argument([
            'userId' => '777',
            'title' => 'title',
            'post' => 'post'
        ]));
    }

    public function testItRequiresUserId(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - userId");

        $command->handle(new Argument([
            'userId' => "",
            'title' => 'title',
            'post' => 'post'
        ]));
    }

    public function testItRequiresTitle(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - title");

        $command->handle(new Argument([
            'userId' => '777',
            'post' => 'post'
        ]));
    }

    public function testItRequiresPost(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - post");

        $command->handle(new Argument([
            'userId' => '777',
            'title' => 'title',
            'blog' => 'post'
        ]));
    }



}