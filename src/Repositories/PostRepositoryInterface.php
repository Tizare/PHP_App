<?php

namespace PHP2\App\Repositories;

use PHP2\App\blog\Post;

interface PostRepositoryInterface
{
    public function get(int $id): Post;
    public function findPost(int $userId, string $title): Post;
}
