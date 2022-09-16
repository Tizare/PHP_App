<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\DeletePostCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class DeletePostFromRequest implements HandlerInterface
{
    private DeletePostCommand $deletePostCommand;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->deletePostCommand = new DeletePostCommand($postRepository);
    }

    public function handle(Request $request): Response
    {
        try {
            $postId['postId'] = $request->query('postId');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->deletePostCommand->handle(new Argument($postId));
        } catch (CommandException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'postId' => $postId['postId'],
            'status' => 'post deleted'
        ]);
    }
}