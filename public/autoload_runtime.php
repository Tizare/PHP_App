<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHP2\App\Authentication\AuthenticationInterface;
use PHP2\App\Authentication\PasswordAuthentication;
use PHP2\App\Authentication\PasswordAuthenticationInterface;
use PHP2\App\Authentication\TokenAuthentication;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\CreateAuthTokenCommand;
use PHP2\App\Commands\CreateAuthTokenCommandInterface;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Container\DiContainer;
use PHP2\App\Repositories\AuthTokenRepository;
use PHP2\App\Repositories\AuthTokenRepositoryInterface;
use PHP2\App\Repositories\CommentRepository;
use PHP2\App\Repositories\CommentRepositoryInterface;
use PHP2\App\Repositories\LikeRepository;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../database/config.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));
$container = new DiContainer();

$container->bind(PDO::class, new PDO (databaseConfig()['sqlite']['DATABASE_URL']));
$container->bind(ConnectorInterface::class, new SqLiteConnector((databaseConfig()['sqlite']['DATABASE_URL'])));
$container->bind(UserRepositoryInterface::class, UserRepository::class);
$container->bind(PostRepositoryInterface::class, PostRepository::class);
$container->bind(CommentRepositoryInterface::class, CommentRepository::class);
$container->bind(LikeRepositoryInterface::class, LikeRepository::class);
$container->bind(AuthTokenRepositoryInterface::class, AuthTokenRepository::class);
$container->bind(PasswordAuthenticationInterface::class, PasswordAuthentication::class);
$container->bind(CreateAuthTokenCommandInterface::class, CreateAuthTokenCommand::class);
$container->bind(TokenAuthenticationInterface::class,TokenAuthentication::class);
$container->bind(LoggerInterface::class,
    (new Logger('php2_logger'))
        ->pushHandler(new StreamHandler(__DIR__ . '/../logs/blog.log'))
        ->pushHandler(new StreamHandler(__DIR__ . '/../logs/blog.error.log', $level = Logger::ERROR, $bubble = false,))
        ->pushHandler(new StreamHandler("php://stdout"))
);

return $container;