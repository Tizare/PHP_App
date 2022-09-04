<?php

namespace PHP2\App\user;

class User
{
    private int $id;
    private string $name;
    private string $surname;

    public function __construct ($name, $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function __toString()
    {
        return $this->name . ' ' . $this->surname;
    }

}