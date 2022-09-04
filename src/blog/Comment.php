<?php

namespace PHP2\App\blog;

class Comment
{
    private int $id;
    private int $userId;
    private int $blogId;
    private string $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->text;
    }
}