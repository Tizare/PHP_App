<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\DeletePostCommand;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeletePostFromRequest implements HandlerInterface
{
    private DeletePostCommand $deletePostCommand;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $postRepository, ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->deletePostCommand = new DeletePostCommand($postRepository, $connector, $logger);
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        try {
            $postId['postId'] = $request->query('postId');
        } catch (HttpException $exception) {
            $this->logger->error($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->deletePostCommand->handle(new Argument($postId));
        } catch (CommandException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'postId' => $postId['postId'],
            'status' => 'post deleted'
        ]);
    }
}