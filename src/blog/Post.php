<?php

namespace PHP2\App\blog;

use PHP2\App\Traits\Id;
use PHP2\App\Traits\UserId;

class Post
{
    use Id;
    use UserId;
    private string $title;
    private string $post;

    public function __construct($title, $post)
    {
        $this->title = $title;
        $this->post = $post;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPost(): string
    {
        return $this->post;
    }

    public function __toString()
    {
        return $this->title . ' >>> ' . $this->post;
    }
}