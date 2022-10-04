<?php

namespace Test;

use PHP2\App\blog\Post;
use PHP2\App\Exceptions\PostNotFoundException;
use PHP2\App\Repositories\PostRepositoryInterface;

class DummyPostRepository implements PostRepositoryInterface
{

    /**
     * @throws PostNotFoundException
     */
    public function get(int $id): Post
    {
        throw new PostNotFoundException("Post with such id - 777 not found");
    }

    /**
     * @throws PostNotFoundException
     */
    public function findPost(int $userId, string $title): Post
    {
        throw new PostNotFoundException("Post with title 'post' from user with id - 777 not found");
    }
}