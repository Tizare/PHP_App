<?php

namespace Test\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\Commands\CreateCommentCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\DummyUsersRepository;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\user\User;
use PHPUnit\Framework\TestCase;
use Test\DummyLogger;

class CreateCommentCommandTest extends TestCase
{
    private function makePostRepositoryDummy(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface {

            public function get(int $id): Post
            {
                throw new PostNotFoundException();
            }
        };
    }

    private function makePostRepository(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface {

            public function get(int $id): Post
            {
                return new Post('title', 'post');
            }
        };
    }

    private function makeUserRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {

            public function get(int $id): User
            {
                return new User('username', 'name', 'surname');
            }

            public function getUserByUsername(string $username): User
            {
                // TODO: Implement getUserByUsername() method.
            }
        };
    }

    public function testItThrowsAnExceptionWhenUserIdAndPostIdNotExist(): void
    {
        $command = new CreateCommentCommand($this->makePostRepositoryDummy(), new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Post with Id - 666 not found" );

        $command->handle(new Argument([
            'userId' => '777',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItThrowsAnExceptionWhenUserIdExistButPostIdNotExist(): void
    {
        $command = new CreateCommentCommand($this->makePostRepositoryDummy(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Post with Id - 666 not found" );

        $command->handle(new Argument([
            'userId' => '777',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItThrowsAnExceptionWhenUserIdNotExistButPostIdExist(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Post with Id - 666 not found" );

        $command->handle(new Argument([
            'userId' => '777',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItRequiresUserId(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - userId");

        $command->handle(new Argument([
            'user_Id' => '',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItRequiresPostId(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - postId");

        $command->handle(new Argument([
            'userId' => '777',
            'post_id' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItRequiresComment(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - comment");

        $command->handle(new Argument([
            'userId' => '777',
            'postId' => '666',
        ]));
    }


}