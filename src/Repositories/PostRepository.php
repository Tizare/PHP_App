<?php

namespace PHP2\App\Repositories;
use PDO;
use PHP2\App\blog\Post;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\PostNotFoundException;

class PostRepository implements PostRepositoryInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(int $id): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM post WHERE id = :postId'
        );

        $statement->execute([':postId' => $id]);

        $postObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$postObj){
            throw new PostNotFoundException("Post with such id - $id not found" . PHP_EOL);
        }

        $post = new Post($postObj->title, $postObj->post);
        $post->setId($postObj->id)->setUserId($postObj->user_id);

        return $post;
    }

    /**
     * @throws PostNotFoundException
     */
    public function findPost(int $userId, string $title): Post
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post WHERE user_id = :userId AND title = :title"
        );

        $statement->execute([
            ':userId' => $userId,
            ':title' => $title
        ]);

        $postObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$postObj){
            throw new PostNotFoundException("Post with title '$title' from user with id - $userId not found");
        }
        $post = new Post($postObj->title, $postObj->post);
        $post->setId($postObj->id)->setUserId($postObj->user_id);

        return $post;
    }
}