<?php

namespace Test\Commands;

use Monolog\Test\TestCase;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Comment;
use PHP2\App\blog\Post;
use PHP2\App\Commands\DeleteCommentCommand;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\ArgumentException;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\CommentRepositoryInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use Test\DummyCommentRepository;
use Test\DummyLogger;
use Test\DummyPostRepository;

class DeleteCommentCommandTest extends TestCase
{
    private function makeCommentRepository(): CommentRepositoryInterface
    {
        return new class implements CommentRepositoryInterface {

            public function get(int $id): Comment
            {
                $comment = new Comment("comment");
                $comment->setUserId("777");
                $comment->setPostId("777");
                return $comment;
            }
        };
    }

    private function makePostRepository(): PostRepositoryInterface
    {
        return new class implements PostRepositoryInterface {

            public function get(int $id): Post
            {
                $post = new Post('title', 'post');
                $post->setUserId('888');
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
    public function testItRequiresCommentId(): void
    {
        $command = new DeleteCommentCommand(new DummyCommentRepository(),
            new DummyPostRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - commentId");

        $command->handle(new Argument(['authUser' => '777']));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresAuthUser(): void
    {
        $command = new DeleteCommentCommand(new DummyCommentRepository(),
            new DummyPostRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument - authUser");

        $command->handle(new Argument(['commentId' => '777']));
    }

    public function testItThrowsAnExceptionWhenCommentNotExist(): void
    {
        $command = new DeleteCommentCommand(new DummyCommentRepository(),
            new DummyPostRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Comment with such id - 777 not exist.");

        $command->handle(new Argument([
            'authUser' => "777",
            'commentId' => "777"
        ]));
    }

    public function testItTrowsAnExceptionWhenUserHasNoRightsToDeleteComment(): void
    {
        $command = new DeleteCommentCommand($this->makeCommentRepository(),
            $this->makePostRepository(), new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('This user can not delete this comment');

        $command->handle(new Argument([
            'authUser' => '222',
            'commentId' => '222'
        ]));

    }
}