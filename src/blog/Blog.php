<?php

namespace PHP2\App\blog;

class Blog
{
    private int $id;
    private int $userId;
    private string $header;
    private string $text;

    public function __construct($header, $text)
    {
        $this->header = $header;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->header . ' >>> ' . $this->text;
    }
}