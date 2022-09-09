<?php

namespace PHP2\App\Repositories;
use PDO;
use PHP2\App\blog\Comment;
use PHP2\App\blog\Post;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\user\User;

class CommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;
    private ?ConnectorInterface $connector;

    public function __construct()
    {
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    public function save(User $user, Post $post, Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comment (post_id, user_id, comment) 
                   VALUES (:post_id, :user_id, :comment)'
        );

        $statement->execute([
            ':post_id' => $post->getId(),
            ':user_id' => $user->getId(),
            ':comment' => $comment->getComment()
        ]);
    }

    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comment WHERE id = :commentId'
        );

        $statement->execute([':commentId' => $id]);

        $commentObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$commentObj){
            throw new CommentNotFoundException("Comment with such id - $id not found");
        };

        $comment = new Comment($commentObj->commetn);
        $comment->setUserId($commentObj->user_id)->setPostId($commentObj->post_id);

        return $comment;
    }
}