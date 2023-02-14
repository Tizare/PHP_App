<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\UserRepositoryInterface;
use PDO;


class CreateUserCommand implements CreateCommandsInterface
{
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ?ConnectorInterface $connector;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $username = $argument->get('username');
        $name = $argument->get('name');
        $surname = $argument->get('surname');

        if($this->userExist($username)){
            throw new CommandException("User with username - $username is already exist" . PHP_EOL);
        } else {
            $statement = $this->connection->prepare(
                'INSERT INTO user (username, name, surname) 
                   VALUES (:username, :name, :surname)'
            );
            $statement->execute([
                ':username' => $username,
                ':name' => $name,
                ':surname' => $surname
            ]);
        }
    }

    private function userExist(string $username): bool
    {
        try {
            $this->userRepository->getUserByUsername($username);
        } catch (UserNotFoundException $exception) {
            return false;
        }
        return true;
    }
}