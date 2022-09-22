<?php

namespace PHP2\App\Repositories;
use PDO;
use PHP2\App\blog\Comment;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Exceptions\CommentNotFoundException;

class CommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->connection = $this->connector->getConnection();
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
            throw new CommentNotFoundException("Comment with such id - $id not found" . PHP_EOL);
        }

        $comment = new Comment($commentObj->comment);
        $comment->setUserId($commentObj->user_id)->setPostId($commentObj->post_id);

        return $comment;
    }
}