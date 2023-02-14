<?php

namespace PHP2\App\Connection;

use PDO;

interface ConnectorInterface
{
    public static function getConnection(): PDO;
}