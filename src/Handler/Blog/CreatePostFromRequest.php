<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class CreatePostFromRequest implements HandlerInterface
{
    private CreatePostCommand $createPostCommand;

    public function __construct(UserRepository $userRepository)
    {
        $this->createPostCommand = new CreatePostCommand($userRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $this->createPostCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['post created!']);
    }
}