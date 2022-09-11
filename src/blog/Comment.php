<?php

namespace PHP2\App\blog;

use PHP2\App\Traits\Id;
use PHP2\App\Traits\UserId;

class Comment
{
    use Id;
    use UserId;
    private int $postId;
    private string $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function setPostId(int $id): self
    {
        $this->postId = $id;
        return $this;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function __toString()
    {
        return $this->comment;
    }
}