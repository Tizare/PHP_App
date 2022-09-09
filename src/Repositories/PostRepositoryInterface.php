<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Post;
use PHP2\App\user\User;

interface PostRepositoryInterface
{
    public function save(User $user, Post $post): void;
    public function get(int $id): Post;
}
