<?php

namespace PHP2\App\Repositories;
use PDO;
use PHP2\App\blog\Post;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\PostNotFoundException;

class PostRepository implements PostRepositoryInterface
{
    private PDO $connection;
    private ?ConnectorInterface  $connector;

    public function __construct()
    {
        $this->connector = $connector ?? new SqLiteConnector();
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
}