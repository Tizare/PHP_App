<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateCommentCommandInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateCommentFromRequest implements HandlerInterface
{
    private CreateCommentCommandInterface $createCommentCommand;
    private LoggerInterface $logger;

    public function __construct(CreateCommentCommandInterface $createCommentCommand, LoggerInterface $logger)
    {
        $this->createCommentCommand = $createCommentCommand;
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        /** TODO: make comment from authen */
        try {
            $this->createCommentCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['comment created!']);
    }
}