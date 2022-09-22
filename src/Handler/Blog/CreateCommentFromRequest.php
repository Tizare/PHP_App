<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateCommentCommand;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateCommentFromRequest implements HandlerInterface
{
    private CreateCommentCommand $createCommentCommand;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, PostRepositoryInterface $postRepository,
                                ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->createCommentCommand = new CreateCommentCommand($postRepository, $userRepository, $connector, $logger);
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        try {
            $this->createCommentCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['comment created!']);
    }
}