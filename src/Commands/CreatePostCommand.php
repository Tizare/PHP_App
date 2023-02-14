<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PDO;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;

class CreatePostCommand implements CreateCommandsInterface
{
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ?ConnectorInterface $connector;

    public function __construct(UserRepositoryInterface $userRepository = null)
    {
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
        $title = $argument->get('title');
        $post = $argument->get('post');

        if($this->userIdNotExist($userId)){
            throw new CommandException("User with Id - $userId not found" . PHP_EOL);
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