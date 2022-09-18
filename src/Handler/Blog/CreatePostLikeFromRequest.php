<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostLikeCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class CreatePostLikeFromRequest implements HandlerInterface
{
    private CreatePostLikeCommand $createPostLikeCommand;

    public function __construct(PostRepository $postRepository, UserRepository $userRepository)
    {
        $this->createPostLikeCommand = new CreatePostLikeCommand($postRepository, $userRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $this->createPostLikeCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['got your Like!']);
    }
}