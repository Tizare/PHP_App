<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;

class CreateCommentCommand implements CreateCommandsInterface
{
    private PostRepositoryInterface $postRepository;
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ?ConnectorInterface $connector;

    public function __construct(PostRepositoryInterface $postRepository = null, UserRepositoryInterface $userRepository = null)
    {
        $this->postRepository = $postRepository ?? new PostRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $userId = $argument->get('userId');
        $postId = $argument->get('postId');
        $comment = $argument->get('comment');

        if($this->userIdNotExist($userId) || $this->postIdNotExist($postId)){
            throw new CommandException("User with Id - $userId or Post with Id - $postId not found" . PHP_EOL);
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