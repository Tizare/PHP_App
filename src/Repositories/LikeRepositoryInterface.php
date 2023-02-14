<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Like;

interface LikeRepositoryInterface
{
    public function getLikeToPostByUser(int $postId, int $userId): Like;
    public function getLikesToPost(int $postId): array;
    public function getLikesToComment(int $commentId): array;
    public function getLikeToCommentByUser(int $commentId, int $userId): Like;

}