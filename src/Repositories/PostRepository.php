<?php

namespace PHP2\App\Repositories;
use PDO;
use PHP2\App\blog\Post;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\user\User;

class PostRepository implements PostRepositoryInterface
{
    private PDO $connection;
    private ?ConnectorInterface  $connector = null;

    public function __construct()
    {
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(User $user, Post $post): void
    {
       $statement = $this->connection->prepare(
           'INSERT INTO post (user_id, title, post) 
                  VALUES (:user_id, :title, :post)'
       );

       $statement->execute(
           [
               ':user_id' => $user->getId(),
               ':title' => $post->getTitle(),
               ':post' => $post->getPost()
           ]
       );

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
            throw new PostNotFoundException("Post with such id - $id not found");
        }

        $post = new Post($postObj->title, $postObj->post);
        $post->setId($postObj->id)->setUserId($postObj->user_id);

        return $post;
    }
}