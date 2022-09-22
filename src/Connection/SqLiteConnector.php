<?php

namespace PHP2\App\Connection;

use PDO;

class SqLiteConnector implements ConnectorInterface
{
    private static PDO $pdo;

    public function __construct(string $dsn)
    {
        self::$pdo = new PDO($dsn);
    }

    public static function getConnection(): PDO
    {
        return self::$pdo;
    }
}