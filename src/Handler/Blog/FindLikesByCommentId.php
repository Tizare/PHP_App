<?php

namespace PHP2\App\Handler\Blog;

use DateTime;
use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindLikesByCommentId implements HandlerInterface
{
    private LikeRepositoryInterface $likeRepository;
    private LoggerInterface $logger;

    public function __construct (LikeRepositoryInterface $likeRepository, LoggerInterface $logger)
    {
        $this->likeRepository = $likeRepository;
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        $this->logger->debug("Begin find Likes to Comment" . (new DateTime())->format('d.m.Y H:i:s'));
        try {
            $commentId = $request->query('commentId');
        } catch (HttpException $exception) {
            $this->logger->error($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $likes = $this->likeRepository->getLikesTocomment($commentId);
        } catch (CommentNotFoundException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }
        $this->logger->debug("End find Likes to Comment" . (new DateTime())->format('d.m.Y H:i:s'));
        return new SuccessfulResponse(['commentId' => $commentId, 'likes' => $likes[0]]);
    }
}