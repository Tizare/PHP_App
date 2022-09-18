<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Comment;

interface CommentRepositoryInterface
{
    public function get (int $id): Comment;

}
