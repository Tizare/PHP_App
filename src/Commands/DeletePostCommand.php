<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeletePostCommand implements CreateCommandsInterface
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

        // TODO: - удаление комментов к посту!

        if ($this->postNotExist($postId)) {
            throw new CommandException("Post with Id - $postId not exist." . PHP_EOL);
        } else {
            $statement = $this->connection->prepare(
                "DELETE FROM post WHERE id = :postId"
            );
            $statement->execute([':postId' => $postId]);
            $this->logger->info("Post $postId deleted");
        }
    }

    private function postNotExist(string $postId): bool
    {
        try {
            $this->postRepository->get($postId);
        } catch (PostNotFoundException $exception) {
            return true;
        }
        return false;
    }

}