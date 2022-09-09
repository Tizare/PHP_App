<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Comment;
use PHP2\App\blog\Post;
use PHP2\App\user\User;

interface CommentRepositoryInterface
{
    public function save (User $user, Post $post, Comment $comment): void;
    public function get (int $id): Comment;

}
