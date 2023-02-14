<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\CreatePostLikeCommandInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePostLikeFromRequest implements HandlerInterface
{
    private CreatePostLikeCommandInterface $createPostLikeCommand;
    private LoggerInterface $logger;
    private TokenAuthenticationInterface $tokenAuthentication;

    public function __construct(CreatePostLikeCommandInterface $createPostLikeCommand,
                                LoggerInterface $logger, TokenAuthenticationInterface $tokenAuthentication)
    {
        $this->createPostLikeCommand = $createPostLikeCommand;
        $this->logger = $logger;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    public function handle(Request $request): Response
    {
        try {
            $author = $this->tokenAuthentication->user($request);
            $like = $request->jsonBody();
            $like['authUser'] = $author->getId();
            $this->createPostLikeCommand->handle(new Argument($like));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['got your Like!']);
    }
}