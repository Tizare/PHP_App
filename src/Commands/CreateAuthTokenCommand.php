<?php

namespace PHP2\App\Commands;

use DateTimeInterface;
use PDO;
use PDOException;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\AuthTokenNotFoundException;
use PHP2\App\user\AuthToken;

class CreateAuthTokenCommand implements CreateAuthTokenCommandInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws AuthTokenNotFoundException
     */
    public function handle(AuthToken $authToken): void
    {
        $query = "
        INSERT INTO token (token, user_id, expires_at) 
        VALUES (:token, :userId, :expiresAt) ON CONFLICT (token) DO UPDATE SET
        expires_at = :expires_at";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->getToken(),
                ':userId' => $authToken->getUser()->getId(),
                ':expiresAt' => $authToken->getExpiresAt()->format(DateTimeInterface::ATOM)
            ]);
        } catch (PDOException $exception) {
            throw new AuthTokenNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}