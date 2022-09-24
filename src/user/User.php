<?php

namespace PHP2\App\user;

use PHP2\App\Traits\Id;

class User
{
    use Id;
    private string $username;
    private string $name;
    private string $surname;
    private string $password;

    public function __construct ($username, $name, $surname, $password)
    {
        $this->username = $username;
        $this->name = $name;
        $this->surname = $surname;
        $this->password = $password;
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

    public function getPassword(): string
    {
        return $this->password;
    }


    public function __toString()
    {
        return $this->name . ' ' . $this->surname;
    }

    private static function hash(string $username, string $password): string
    {
        return hash('sha256', $username. $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->password === self::hash($this->username, $password);
    }

}