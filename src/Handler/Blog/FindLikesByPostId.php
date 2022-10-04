<?php

namespace PHP2\App\Handler\Blog;

use DateTime;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\LikeRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindLikesByPostId implements HandlerInterface
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
        $this->logger->debug("Begin find likes to Post" . (new DateTime())->format('d.m.Y H:i:s'));
        try {
            $postId = $request->query('postId');
        } catch (HttpException $exception) {
            $this->logger->error($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $likes = $this->likeRepository->getLikesToPost($postId);
        } catch (PostNotFoundException $exception) {
            $this->logger->warning($exception->getMessage());
            return new ErrorResponse($exception->getMessage());
        }

        $this->logger->debug("End find likes to Post" . (new DateTime())->format('d.m.Y H:i:s'));
        return new SuccessfulResponse(['postId' => $postId, 'likes' => $likes[0]]);
    }
}