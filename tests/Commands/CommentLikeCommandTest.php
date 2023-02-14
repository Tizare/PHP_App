<?php

namespace Test\Commands;

use Monolog\Test\TestCase;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Comment;
use PHP2\App\blog\Like;
use PHP2\App\Commands\CommentLikeCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\LikeException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\CommentRepositoryInterface;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\user\User;
use Test\DummyCommentRepository;
use Test\DummyUsersRepository;

class CommentLikeCommandTest extends TestCase
{
    private function makeLikeRepository(): LikeRepositoryInterface
    {
        return new class implements LikeRepositoryInterface {

            public function getLikeToPostByUser(int $postId, int $userId): Like
            {
                throw new LikeException();
            }

            public function getLikesToPost(int $postId): array
            {
                throw new LikeException();
            }

            public function getLikesToComment(int $commentId): array
            {
                throw new LikeException();
            }

            public function getLikeToCommentByUser(int $commentId, int $userId): Like
            {
                throw new LikeException();
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
    private function makeCommentRepository(): CommentRepositoryInterface
    {
        return new class implements CommentRepositoryInterface {

            public function get(int $id): Comment
            {
                return new Comment('comment');
            }
        };
    }

    /**
     * @throws CommandException
     */
    public function testIrRequiresUserId(): void
    {
        $command = new CommentLikeCommand(new DummyUsersRepository(), new DummyCommentRepository(),
            $this->makeLikeRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - userId");

        $command->handle(new Argument(['commentId' => '777']));
    }

    /**
     * @throws CommandException
     */
    public function testIrRequiresCommentId(): void
    {
        $command = new CommentLikeCommand(new DummyUsersRepository(), new DummyCommentRepository(),
            $this->makeLikeRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - commentId");

        $command->handle(new Argument(['userId' => '777']));
    }

    public function testItThrowAnExceptionWhenUserIdAndCommentIdNotExist(): void
    {
        $command = new CommentLikeCommand(new DummyUsersRepository(), new DummyCommentRepository(),
            $this->makeLikeRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Comment with Id - 777 not found");

        $command->handle(new Argument([
            'userId' => '777',
            'commentId' => '777'
        ]));
    }

    public function testItThrowAnExceptionWhenUserIdNotExist(): void
    {
        $command = new CommentLikeCommand(new DummyUsersRepository(), $this->makeCommentRepository(),
            $this->makeLikeRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Comment with Id - 777 not found");

        $command->handle(new Argument([
            'userId' => '777',
            'commentId' => '777'
        ]));
    }

    public function testItThrowAnExceptionWhenCommentIdNotExist(): void
    {
        $command = new CommentLikeCommand($this->makeUserRepository(), new DummyCommentRepository(),
            $this->makeLikeRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User with Id - 777 or Comment with Id - 777 not found");

        $command->handle(new Argument([
            'userId' => '777',
            'commentId' => '777'
        ]));
    }
}