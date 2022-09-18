<?php

use PHP2\App\Handler\Blog\CommentLikeFromRequest;
use PHP2\App\Handler\Blog\CreateCommentFromRequest;
use PHP2\App\Handler\Blog\CreatePostFromRequest;
use PHP2\App\Handler\Blog\CreatePostLikeFromRequest;
use PHP2\App\Handler\Blog\DeletePostFromRequest;
use PHP2\App\Handler\Blog\FindLikesByCommentId;
use PHP2\App\Handler\Blog\FindLikesByPostId;
use PHP2\App\Handler\Blog\FindPostById;
use PHP2\App\Handler\Users\FindByUserName;
use PHP2\App\Repositories\CommentRepository;
use PHP2\App\Repositories\LikeRepository;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Request\Request;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Response\ErrorResponse;

require_once __DIR__ . '\autoload_runtime.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

try {
    $path = $request->path();
} catch (HttpException $ex) {
    (new ErrorResponse($ex->getMessage()))->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $exception) {
    (new ErrorResponse("Cannot get method"))->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => new FindByUserName(new UserRepository()),
        '/post/like' => new FindLikesByPostId(new LikeRepository()),
        '/post/comment/like' => new FindLikesByCommentId(new LikeRepository()),
        '/post/show' => new FindPostById(new PostRepository()),
    ],
    'POST' => [
        '/post/create' => new CreatePostFromRequest(new UserRepository()),
        '/post/comment' => new CreateCommentFromRequest(new UserRepository(), new PostRepository()),
        '/post/like'=> new CreatePostLikeFromRequest(new PostRepository(), new UserRepository()),
        '/post/comment/like' => new CommentLikeFromRequest(new UserRepository(), new CommentRepository())
    ],
    'DELETE' => [
        '/post' => new DeletePostFromRequest(new PostRepository()),
    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Such method not found"))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Such path not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $exception) {
    (new ErrorResponse($exception->getMessage()))->send();
}

$response->send();
