<?php

use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHP2\App\Authentication\PasswordAuthentication;
use PHP2\App\Authentication\PasswordAuthenticationInterface;
use PHP2\App\Authentication\TokenAuthentication;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\CommentLikeCommand;
use PHP2\App\Commands\CommentLikeCommandInterface;
use PHP2\App\Commands\CreateAuthTokenCommand;
use PHP2\App\Commands\CreateAuthTokenCommandInterface;
use PHP2\App\Commands\CreateCommentCommand;
use PHP2\App\Commands\CreateCommentCommandInterface;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Commands\CreatePostCommandInterface;
use PHP2\App\Commands\CreatePostLikeCommand;
use PHP2\App\Commands\CreatePostLikeCommandInterface;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Commands\CreateUserCommandInterface;
use PHP2\App\Commands\DeletePostCommand;
use PHP2\App\Commands\DeletePostCommandInterface;
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
$container->bind(CommentLikeCommandInterface::class,CommentLikeCommand::class);
$container->bind(CreateCommentCommandInterface::class,CreateCommentCommand::class);
$container->bind(CreatePostCommandInterface::class,CreatePostCommand::class);
$container->bind(CreatePostLikeCommandInterface::class,CreatePostLikeCommand::class);
$container->bind(CreateUserCommandInterface::class,CreateUserCommand::class);
$container->bind(DeletePostCommandInterface::class,DeletePostCommand::class);
$container->bind(LoggerInterface::class,
    (new Logger('php2_logger'))
        ->pushHandler(new StreamHandler(__DIR__ . '/../logs/blog.log'))
        ->pushHandler(new StreamHandler(__DIR__ . '/../logs/blog.error.log', $level = Logger::ERROR, $bubble = false,))
//        ->pushHandler(new StreamHandler("php://stdout"))
);

$faker = new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(\Faker\Generator::class, $faker);

return $container;