<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PDO;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePostCommand implements CreateCommandsInterface
{
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, ConnectorInterface $connector,
                                LoggerInterface $logger)
    {
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
        $this->logger->info("Begin create Post");

        $userId = $argument->get('authUser');
        $title = $argument->get('title');
        $post = $argument->get('post');

        if($this->userIdNotExist($userId)){
            throw new CommandException("User with Id - $userId not found");
        } else {
            $statement = $this->connection->prepare(
                'INSERT INTO post (user_id, title, post) 
                   VALUES (:user_id, :title, :post)'
            );
            $statement->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':post' => $post
            ]);
            $this->logger->info("Post created (userId = $userId, title = $title");
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
}