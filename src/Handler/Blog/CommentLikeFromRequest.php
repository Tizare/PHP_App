<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CommentLikeCommandInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CommentLikeFromRequest implements HandlerInterface
{
    private CommentLikeCommandInterface $commentLikeCommand;
    private LoggerInterface $logger;

    public function __construct (CommentLikeCommandInterface $commentLikeCommand,
                                 LoggerInterface $logger)
    {
        $this->commentLikeCommand = $commentLikeCommand;
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        try {
            $this->commentLikeCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['got you like!']);
    }
}