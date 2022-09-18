<?php

namespace PHP2\App\Handler\Blog;

use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class FindLikesByCommentId implements HandlerInterface
{
    private LikeRepositoryInterface $likeRepository;

    public function __construct (LikeRepositoryInterface $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $commentId = $request->query('commentId');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $likes = $this->likeRepository->getLikesTocomment($commentId);
        } catch (CommentNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse(['commentId' => $commentId, 'likes' => $likes[0]]);
    }
}