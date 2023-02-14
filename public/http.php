<?php

/** @var ContainerInterface $container */
/** @var Request $request */

use PHP2\App\Handler\Blog\CommentLikeFromRequest;
use PHP2\App\Handler\Blog\CreateCommentFromRequest;
use PHP2\App\Handler\Blog\CreatePostFromRequest;
use PHP2\App\Handler\Blog\CreatePostLikeFromRequest;
use PHP2\App\Handler\Blog\DeletePostFromRequest;
use PHP2\App\Handler\Blog\FindLikesByCommentId;
use PHP2\App\Handler\Blog\FindLikesByPostId;
use PHP2\App\Handler\Blog\FindPostById;
use PHP2\App\Handler\Users\CreateUserFromRequest;
use PHP2\App\Handler\Users\FindByUserName;
use PHP2\App\Handler\Users\LoginHandle;
use PHP2\App\Request\Request;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Response\ErrorResponse;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = require_once __DIR__ . '\autoload_runtime.php';
$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $ex) {
    $logger->warning($ex->getMessage());
    (new ErrorResponse($ex->getMessage()))->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $exception) {
    $logger->warning($exception->getMessage());
    (new ErrorResponse("Cannot get method"))->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => FindByUserName::class,
        '/post/like' => FindLikesByPostId::class,
        '/post/comment/like' => FindLikesByCommentId::class,
        '/post/show' => FindPostById::class,
    ],
    'POST' => [
        '/post/create' => CreatePostFromRequest::class,
        '/post/comment' => CreateCommentFromRequest::class,
        '/post/like'=> CreatePostLikeFromRequest::class,
        '/post/comment/like' => CommentLikeFromRequest::class,
        '/user/create' => CreateUserFromRequest::class,
        '/login' => LoginHandle::class
    ],
    'DELETE' => [
        '/post' => DeletePostFromRequest::class,
    ]
];

if (!array_key_exists($method, $routes)) {
    $message = "Such method not found";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    $message = "Such path not found";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$action = $container->get(($routes[$method][$path]));

try {
    $response = $action->handle($request);
} catch (Exception $exception) {
    $logger->error($exception->getMessage(), ['exception' => $exception]);
    (new ErrorResponse($exception->getMessage()))->send();
}

$response->send();
