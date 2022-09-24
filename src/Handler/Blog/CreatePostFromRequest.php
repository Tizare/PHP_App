<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\AuthException;
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
    private TokenAuthenticationInterface $tokenAuthentication;

    public function __construct(UserRepositoryInterface $userRepository, ConnectorInterface $connector,
                                LoggerInterface $logger, TokenAuthenticationInterface $tokenAuthentication)
    {
        $this->createPostCommand = new CreatePostCommand($userRepository, $connector, $logger);
        $this->logger = $logger;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    public function handle(Request $request): Response
    {

        try {
            $author = $this->tokenAuthentication->user($request);
            $post = $request->jsonBody();
            $post["authUser"] = $author->getId();
            $this->createPostCommand->handle(new Argument($post));
        } catch (CommandException|AuthException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['post created!']);
    }
}