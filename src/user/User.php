<?php

namespace PHP2\App\user;

use PHP2\App\Traits\Id;

class User
{
    use Id;
    private string $username;
    private string $name;
    private string $surname;

    public function __construct ($username, $name, $surname)
    {
        $this->username = $username;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function __toString()
    {
        return $this->name . ' ' . $this->surname;
    }

}