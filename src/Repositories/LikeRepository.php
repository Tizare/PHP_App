<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Like;
use PHP2\App\Connection\ConnectorInterface;
use PHP2\App\Connection\SqLiteConnector;
use PDO;
use PHP2\App\Exceptions\LikeException;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Response\ErrorResponse;

class LikeRepository implements LikeRepositoryInterface
{
    private PDO $connection;
    private ConnectorInterface $connector;
    private PostRepositoryInterface $postRepository;
    private CommentRepositoryInterface $commentRepository;

    public function __construct(?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }

    public function mapLike(object $likeObj): Like
    {
        if(property_exists($likeObj, 'post_id')){
            $like = new Like($likeObj->user_id, $likeObj->post_id);
            $like->setId($likeObj->id);
        } else {
            $like = new Like($likeObj->user_id, $likeObj->comment_id);
        }

        return $like;
    }

    /**
     * @throws LikeException
     */
    public function getLikeToPostByUser(int $postId, int $userId): Like
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post_like WHERE post_id = :post_id AND user_id = :user_id"
        );
        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
        ]);
        $likeObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$likeObj) {
            throw new LikeException("Such like not found (postID - $postId, userId - $userId)");
        }

        return $this->mapLike($likeObj);
    }

    /**
     * @throws LikeException
     */
    public function getLikesToPost(int $postId): array
    {
        $this->postRepository->get($postId);

        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM post_like WHERE post_id = :post_id'
        );

        $statement->execute([':post_id' => $postId]);
        $likeObj = $statement->fetch(PDO::FETCH_NUM);

        if(!$likeObj) {
            throw new LikeException("No likes to post $postId");
        }

        return $likeObj;
    }

    public function getLikesToComment(int $commentId): array
    {
        $this->commentRepository->get($commentId);

        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM comment_like WHERE comment_id = :comment_id'
        );

        $statement->execute([':comment_id' => $commentId]);
        $likeObj = $statement->fetch(PDO::FETCH_NUM);

        if(!$likeObj) {
            throw new LikeException("No likes to post $commentId");
        }

        return $likeObj;
    }

    /**
     * @throws LikeException
     */
    public function getLikeToCommentByUser(int $commentId, int $userId): Like
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment_like WHERE comment_id = :comment_id AND user_id = :user_id"
        );
        $statement->execute([
            ':comment_id' => $commentId,
            ':user_id' => $userId,
        ]);
        $likeObj = $statement->fetch(PDO::FETCH_OBJ);

        if(!$likeObj) {
            throw new LikeException("Such like not found (commentID - $commentId, userId - $userId)");
        }

        return $this->mapLike($likeObj);
    }
}