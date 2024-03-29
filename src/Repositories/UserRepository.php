<?php

namespace PHP2\App\Repositories;

use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\user\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
    }

    public function mapUser(object $userObj): User
    {
        $user = new User($userObj->username, $userObj->name, $userObj->surname, $userObj->password);

        $user->setId($userObj->id);

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM user WHERE id = :userId"
        );
        $statement->execute([
            ':userId' => $id
        ]);
        $userObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$userObj) {
            throw new UserNotFoundException("User with id - $id not found" . PHP_EOL);
        }

        return $this->mapUser($userObj);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM user WHERE username = :username"
        );
        $statement->execute([':username' => $username]);
        $userObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$userObj) {
            throw new UserNotFoundException("User with username - $username not exist" . PHP_EOL);
        }

        return $this->mapUser($userObj);
    }

}