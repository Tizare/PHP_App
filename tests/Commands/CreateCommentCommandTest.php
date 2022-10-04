<?php

namespace Test\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\Commands\CreateCommentCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\user\User;
use PHPUnit\Framework\TestCase;
use Test\DummyLogger;
use Test\DummyPostRepository;
use Test\DummyUsersRepository;

class CreateCommentCommandTest extends TestCase
{

    private function makePostRepository(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface {

            public function get(int $id): Post
            {
                return new Post('title', 'post');
            }

            public function findPost(int $userId, string $title): Post
            {
                throw new PostNotFoundException();
            }
        };
    }

    private function makeUserRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {

            public function get(int $id): User
            {
                return new User('username', 'name', 'surname', 'password');
            }

            public function getUserByUsername(string $username): User
            {
                throw new UserNotFoundException();
            }
        };
    }

    public function testItThrowsAnExceptionWhenUserIdAndPostIdNotExist(): void
    {
        $command = new CreateCommentCommand(new DummyPostRepository(), new DummyUsersRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Post with Id - 666 not found" );

        $command->handle(new Argument([
            'authUser' => '777',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    public function testItThrowsAnExceptionWhenUserIdExistButPostIdNotExist(): void
    {
        $command = new CreateCommentCommand(new DummyPostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Post with Id - 666 not found" );

        $command->handle(new Argument([
            'authUser' => '777',
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
            'authUser' => '777',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresAuthUser(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - authUser");

        $command->handle(new Argument([
            'user_Id' => '',
            'postId' => '666',
            'comment' => 'comment'
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresPostId(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - postId");

        $command->handle(new Argument([
            'authUser' => '777',
            'post_id' => '666',
            'comment' => 'comment'
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresComment(): void
    {
        $command = new CreateCommentCommand($this->makePostRepository(), $this->makeUserRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - comment");

        $command->handle(new Argument([
            'authUser' => '777',
            'postId' => '666',
        ]));
    }


}