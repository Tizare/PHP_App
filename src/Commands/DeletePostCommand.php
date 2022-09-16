<?php

namespace PHP2\App\Commands;

use PDO;
use PHP2\App\Argument\Argument;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\PostRepositoryInterface;

class DeletePostCommand implements CreateCommandsInterface
{
    private PostRepositoryInterface $postRepository;
    private PDO $connection;
    private ?ConnectorInterface $connector;

    public function __construct(PostRepositoryInterface $postRepository = null)
    {
        $this->postRepository = $postRepository ?? new PostRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws CommandException
     */
    public function handle(Argument $argument): void
    {
        $postId = $argument->get('postId');

        /** TODO - удаление комментов к посту! */

        if ($this->postNotExist($postId)) {
            throw new CommandException("Post with Id - $postId not exist." . PHP_EOL);
        } else {
            $statement = $this->connection->prepare(
                "DELETE FROM post WHERE id = :postId"
            );
            $statement->execute([':postId' => $postId]);
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