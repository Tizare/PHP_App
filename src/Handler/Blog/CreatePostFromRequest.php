<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePostFromRequest implements HandlerInterface
{
    private CreatePostCommand $createPostCommand;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->createPostCommand = new CreatePostCommand($userRepository, $connector, $logger);
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        try {
            $this->createPostCommand->handle(new Argument($request->jsonBody()));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['post created!']);
    }
}