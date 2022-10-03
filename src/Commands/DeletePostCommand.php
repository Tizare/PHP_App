<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeletePostCommand implements DeletePostCommandInterface
{
    private PostRepositoryInterface $postRepository;
    private PDO $connection;
    private ConnectorInterface $connector;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $postRepository, ConnectorInterface $connector, LoggerInterface $logger)
    {
        $this->postRepository = $postRepository;
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
        $this->logger = $logger;
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $this->logger->info("Begin delete Post");
        $postId = $argument->get('postId');
        $authUser = $argument->get('authUser');

        $post = $this->postExist($postId);
        if (!$this->postMayBeDeleted($post, $authUser)) {
            $this->logger->warning("This user $authUser can not delete this post $postId");
            throw new CommandException("This user can not delete this post");
        } else {
            $param = [':postId' => $postId];

            $statementFirst = $this->connection->prepare(
                "DELETE FROM comment WHERE post_id = :postId"
            );
            $statementFirst->execute($param);

            $statementSecond = $this->connection->prepare(
                'DELETE FROM post_like WHERE post_id = :postId'
            );
            $statementSecond->execute($param);

            $statement = $this->connection->prepare(
                "DELETE FROM post WHERE id = :postId"
            );
            $statement->execute($param);

            $this->logger->info("Post $postId deleted with all comments");
        }
    }

    /**
     * @throws CommandException
     */
    private function postExist(string $postId): Post
    {
        try {
            $post = $this->postRepository->get($postId);
        } catch (PostNotFoundException $exception) {
            throw new CommandException("Post with Id - $postId not exist.");
        }
        return $post;
    }

    private function postMayBeDeleted(Post $post, string $userId): bool
    {
        if ($post->getUserId() == $userId) {
            return true;
        }
        return false;
    }

}