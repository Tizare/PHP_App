<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\Exceptions\LikeException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\CommentRepository;
use PHP2\App\Repositories\CommentRepositoryInterface;
use PHP2\App\Repositories\LikeRepository;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;

class CommentLikeCommand implements CreateCommandsInterface
{
    private UserRepositoryInterface $userRepository;
    private CommentRepositoryInterface $commentRepository;
    private LikeRepository $likeRepository;
    private PDO $connection;
    private ConnectorInterface $connector;

    public function __construct(UserRepositoryInterface $userRepository = null, CommentRepositoryInterface $commentRepository = null)
    {
        $this->commentRepository = $commentRepository ?? new CommentRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->likeRepository = new LikeRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $userId = $argument->get("userId");
        $commentId = $argument->get("commentId");

        if ($this->likeExist($commentId, $userId)) {
            $this->deleteCommentLike($commentId, $userId);
            return;
        }

        if ($this->userIdNotExist($userId) || $this->commentIdNotExist($commentId)) {
            throw new CommandException("User with Id - $userId or Comment with Id - $commentId not found");
        } else {
            $this->createCommentLike($commentId, $userId);
        }
    }

    private function createCommentLike($commentId, $userId): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comment_like (comment_id, user_id) 
                VALUES (:comment_id, :user_id)'
        );
        $statement->execute([
            ':comment_id' => $commentId,
            ':user_id' => $userId,
        ]);

    }

    private function deleteCommentLike($commentId, $userId): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comment_like 
                WHERE comment_id = :comment_id AND user_id = :user_id'
        );
        $statement->execute([
            ':comment_id' => $commentId,
            ':user_id' => $userId,
        ]);

    }

    private function userIdNotExist(string $userId): bool
    {
        try {
            $this->userRepository->get($userId);
        } catch (UserNotFoundException $exception) {
            return true;
        }
        return false;
    }

    private function commentIdNotExist(string $commentId): bool
    {
        try {
            $this->commentRepository->get($commentId);
        } catch (CommentNotFoundException $exception) {
            return true;
        }
        return false;
    }

    private function likeExist(string $commentId, string $userId): bool
    {
        try {
            $this->likeRepository->getLikeToCommentByUser($commentId, $userId);
        } catch (LikeException $exception) {
            return false;
        }
        return true;
    }
}