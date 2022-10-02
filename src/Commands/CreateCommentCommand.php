<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateCommentCommand implements CreateCommentCommandInterface
{
    private PostRepositoryInterface $postRepository;
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $postRepository, UserRepositoryInterface $userRepository,
                                ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
        $this->logger = $logger;
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $this->logger->info("Begin create comment");
        $userId = $argument->get('userId');
        $postId = $argument->get('postId');
        $comment = $argument->get('comment');

        if($this->userIdNotExist($userId) || $this->postIdNotExist($postId)){
            throw new CommandException("User with Id - $userId or Post with Id - $postId not found");
        } else {
            $statement = $this->connection->prepare(
                'INSERT INTO comment (post_id, user_id, comment) 
                   VALUES (:post_id, :user_id, :comment)'
            );
            $statement->execute([
                ':post_id' => $postId,
                ':user_id' => $userId,
                ':comment' => $comment
            ]);
            $this->logger->info("Comment created (postId = $userId, userId = $userId)");
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
}