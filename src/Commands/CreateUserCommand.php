<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\UserRepositoryInterface;
use PDO;
use Psr\Log\LoggerInterface;


class CreateUserCommand implements CreateCommandsInterface
{
    private UserRepositoryInterface $userRepository;
    private PDO $connection;
    private ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, ConnectorInterface $connector, LoggerInterface $logger)
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
        $this->logger->info("Create user command start");

        $username = $argument->get('username');
        $name = $argument->get('name');
        $surname = $argument->get('surname');
        $password = $argument->get('password');
        $hashPassword = hash("sha256", $username . $password);

        if($this->userExist($username)){
            throw new CommandException("User with username - $username is already exist");
        } else {

            $statement = $this->connection->prepare(
                'INSERT INTO user (username, name, surname, password) 
                   VALUES (:username, :name, :surname, :password)'
            );
            $statement->execute([
                ':username' => $username,
                ':name' => $name,
                ':surname' => $surname,
                ':password' => $hashPassword
            ]);
            $this->logger->info("User create (username = $username");
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