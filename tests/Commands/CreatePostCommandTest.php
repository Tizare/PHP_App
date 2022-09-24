<?php

namespace Test\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Repositories\DummyUsersRepository;
use PHPUnit\Framework\TestCase;
use Test\DummyLogger;

class CreatePostCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserIdNotExist(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 not found");

        $command->handle(new Argument([
            'authUser' => '777',
            'title' => 'title',
            'post' => 'post'
        ]));
    }

    public function testItRequiresAuthUser(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - authUser");

        $command->handle(new Argument([
            'userId' => "",
            'title' => 'title',
            'post' => 'post'
        ]));
    }

    public function testItRequiresTitle(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - title");

        $command->handle(new Argument([
            'authUser' => '777',
            'post' => 'post'
        ]));
    }

    public function testItRequiresPost(): void
    {
        $command = new CreatePostCommand(new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - post");

        $command->handle(new Argument([
            'authUser' => '777',
            'title' => 'title',
            'blog' => 'post'
        ]));
    }



}