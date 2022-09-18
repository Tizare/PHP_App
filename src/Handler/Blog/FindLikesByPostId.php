<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\LikeException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class FindLikesByPostId implements HandlerInterface
{
    private LikeRepositoryInterface $likeRepository;

    public function __construct (LikeRepositoryInterface $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $postId = $request->query('postId');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $likes = $this->likeRepository->getLikesToPost($postId);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['postId' => $postId, 'likes' => $likes[0]]);
    }
}