<?php

namespace PHP2\App\blog;

use PHP2\App\Traits\Id;

class Like
{
    use Id;
    private int $userId;
    private int $postId;

    public function __construct(int $userId, int $postId)
    {
        $this->userId = $userId;
        $this->postId = $postId;
    }
}