<?php

namespace Test;

use PHP2\App\blog\Comment;
use PHP2\App\Exceptions\CommentNotFoundException;
use PHP2\App\Repositories\CommentRepositoryInterface;

class DummyCommentRepository implements CommentRepositoryInterface
{

    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        throw new CommentNotFoundException();
    }
}