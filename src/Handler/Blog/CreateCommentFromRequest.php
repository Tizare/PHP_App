<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateCommentCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class CreateCommentFromRequest implements HandlerInterface
{
    private CreateCommentCommand $createCommentCommand;

    public function __construct(UserRepositoryInterface $userRepository, PostRepositoryInterface $postRepository)
    {
        $this->createCommentCommand = new CreateCommentCommand($postRepository, $userRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $this->createCommentCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['comment created!']);
    }
}