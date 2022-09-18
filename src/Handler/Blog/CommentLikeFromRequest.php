<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CommentLikeCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\CommentRepository;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class CommentLikeFromRequest implements HandlerInterface
{
    private CommentLikeCommand $commentLikeCommand;

    public function __construct (UserRepository $userRepository, CommentRepository $commentRepository)
    {
        $this->commentLikeCommand = new CommentLikeCommand($userRepository, $commentRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $this->commentLikeCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['got you like!']);
    }
}