<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\CreateCommentCommandInterface;
use PHP2\App\Exceptions\AuthException;
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
    private TokenAuthenticationInterface $tokenAuthentication;

    public function __construct(CreateCommentCommandInterface $createCommentCommand,
                                LoggerInterface $logger, TokenAuthenticationInterface $tokenAuthentication)
    {
        $this->createCommentCommand = $createCommentCommand;
        $this->logger = $logger;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    public function handle(Request $request): Response
    {

        try {
            $author = $this->tokenAuthentication->user($request);
            $comment = $request->jsonBody();
            $comment['authUser'] = $author->getId();
            $this->createCommentCommand->handle(new Argument($comment));
        } catch (CommandException|AuthException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['comment created!']);
    }
}