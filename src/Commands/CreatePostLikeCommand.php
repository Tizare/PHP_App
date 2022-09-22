<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\LikeException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\LikeRepository;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PDO;
use Psr\Log\LoggerInterface;

class CreatePostLikeCommand implements CreateCommandsInterface
{
    private PostRepositoryInterface $postRepository;
    private UserRepositoryInterface $userRepository;
    private LikeRepository $likeRepository;
    private PDO $connection;
    private ?ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $postRepository, UserRepositoryInterface $userRepository,
                                LikeRepositoryInterface $likeRepository, ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
        $this->logger = $logger;
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $this->logger->info("Begin create Post Like");
        $userId = $argument->get('userId');
        $postId = $argument->get('postId');

        if ($this->likeExist($postId, $userId)) {
            throw new CommandException("Like already exist");
        }

        if ($this->userIdNotExist($userId) || $this->postIdNotExist($postId)) {
            throw new CommandException("User with Id - $userId or Post with Id - $postId not found");
        } else {
            $statement = $this->connection->prepare(
                'INSERT INTO post_like (post_id, user_id) 
                    VALUES (:post_id, :user_id)'
            );
            $statement->execute([
                ':post_id' => $postId,
                ':user_id' => $userId,
            ]);
            $this->logger->info("Created Post Like (postId = $postId, userId = $userId)");
        }
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

    private function postIdNotExist(string $postId): bool
    {
        try {
            $this->postRepository->get($postId);
        } catch (PostNotFoundException $exception) {
            return true;
        }
        return false;
    }

    private function likeExist(string $postId, string $userId): bool
    {
        try {
            $this->likeRepository->getLikeToPostByUser($postId, $userId);
        } catch (LikeException $exception) {
            return false;
        }
        return true;
    }
}