<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Comment;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\Repositories\CommentRepositoryInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteCommentCommand implements DeleteCommentCommandInterface
{
    private CommentRepositoryInterface $commentRepository;
    private PostRepositoryInterface $postRepository;
    private PDO $connection;
    private ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(CommentRepositoryInterface $commentRepository, PostRepositoryInterface $postRepository,
                                ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
        $this->logger = $logger;
    }


    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $this->logger->info("Begin delete comment");
        $commentId = $argument->get('commentId');
        $authUser = $argument->get('authUser');

        $comment = $this->commentExist($commentId);
        $postId = $comment->getPostId();
        $post = $this->postRepository->get($postId);

        if (!$this->commentMayBeDeleted($comment, $authUser, $post->getUserId())) {
            $this->logger->warning("This user $authUser can not delete this comment $commentId");
            throw new CommandException("This user can not delete this comment");
        } else {
            $statementFirst = $this->connection->prepare(
                'DELETE FROM comment_like WHERE comment_id = :commentId'
            );
            $statementFirst->execute([':commentId' => $commentId]);

            $statement = $this->connection->prepare(
                "DELETE FROM comment WHERE id = :commentId"
            );
            $statement->execute([':commentId' => $commentId]);
            $this->logger->info("Comment $commentId deleted with all likes");
        }

    }

    /**
     * @throws CommandException
     */
    private function commentExist(string $commentId): Comment
    {
        try {
            $comment = $this->commentRepository->get($commentId);
        } catch (CommentNotFoundException $exception) {
            throw new CommandException("Comment with such id - $commentId not exist.");
        }
        return $comment;
    }

    private function commentMayBeDeleted(Comment $comment, string $userId, string $postOwnerId): bool
    {
        if($comment->getUserId() == $userId || $comment->getUserId() == $postOwnerId) {
            return true;
        }
        return false;
    }
}