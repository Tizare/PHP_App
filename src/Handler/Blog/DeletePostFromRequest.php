<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Authentication\TokenAuthenticationInterface;
use PHP2\App\Commands\DeletePostCommandInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeletePostFromRequest implements HandlerInterface
{
    private DeletePostCommandInterface $deletePostCommand;
    private LoggerInterface $logger;
    private TokenAuthenticationInterface $tokenAuthentication;

    public function __construct(DeletePostCommandInterface $deletePostCommand,
                                LoggerInterface $logger, TokenAuthenticationInterface $tokenAuthentication)
    {
        $this->deletePostCommand = $deletePostCommand;
        $this->logger = $logger;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    public function handle(Request $request): Response
    {
        try {
            $post['postId'] = $request->query('postId');
        } catch (HttpException $exception) {
            $this->logger->error($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $author = $this->tokenAuthentication->user($request);
            $post['authUser'] = $author->getId();
            $this->deletePostCommand->handle(new Argument($post));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'postId' => $post['postId'],
            'status' => 'post deleted'
        ]);
    }
}