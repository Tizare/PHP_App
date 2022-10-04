<?php

namespace Test\Commands;

use Monolog\Test\TestCase;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\Commands\DeletePostCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;
use Test\DummyLogger;
use Test\DummyPostRepository;

class DeletePostCommandTest extends TestCase
{
    private function makePostRepository(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface {

            public function get(int $id): Post
            {
                $post = new Post('title', 'post');
                $post->setUserId('777');
                return $post;

            }

            public function findPost(int $userId, string $title): Post
            {
                throw new PostNotFoundException();
            }
        };
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresPostId(): void
    {
        $command = new DeletePostCommand($this->makePostRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - postId");

        $command->handle(new Argument(["authUser" => "777"]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresAuthUser(): void
    {
        $command = new DeletePostCommand($this->makePostRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - authUser");

        $command->handle(new Argument(["postId" => "777"]));
    }

    /**
     * @throws CommandException
     */
    public function testItThrowsAnExceptionWhenPostIdNotExist(): void
    {
        $command = new DeletePostCommand(new DummyPostRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Post with Id - 777 not exist");

        $command->handle(new Argument([
            'authUser' => '777',
            'postId' => '777'
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItThrowsAnExceptionWhenUserHasNoRightsToDeletePost(): void
    {
        $command = new DeletePostCommand($this->makePostRepository(),
            new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("This user can not delete this post");

        $command->handle(new Argument([
            'authUser' => '666',
            'postId' => '777'
        ]));
    }

}