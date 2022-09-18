<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class FindPostById implements HandlerInterface
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $postId = $request->query('postId');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $post = $this->postRepository->get($postId);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'postId' => $postId,
            'title' => $post->getTitle(),
            'post' => $post->getPost(),
        ]);
    }
}