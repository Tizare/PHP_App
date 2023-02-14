<?php

namespace PHP2\App\Repositories;

use DateTime;
use PDO;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\AuthTokenNotFoundException;
use PHP2\App\user\AuthToken;

class AuthTokenRepository implements AuthTokenRepositoryInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;
    private UserRepositoryInterface $userRepository;

    public function __construct(ConnectorInterface $connector, UserRepositoryInterface  $userRepository)
    {
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
        $this->userRepository = $userRepository;
    }

    /**
     * @throws AuthTokenNotFoundException
     */
    public function get(string $token): AuthToken
    {

        $statement = $this->connection->prepare(
            'SELECT * FROM token WHERE token = :token'
        );
        $statement->execute([':token' => $token]);
        $tokenObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$tokenObj) {
            throw new AuthTokenNotFoundException("Auth token $token not found");
        }

        return $this->mapToken($tokenObj);
    }

    /**
     * @throws /Exception
     */
    public function mapToken(object $tokenObj): AuthToken
    {
        return new AuthToken(
            $tokenObj->token,
            $this->userRepository->get($tokenObj->user_id),
            new DateTime($tokenObj->expires_at)
        );
    }

}