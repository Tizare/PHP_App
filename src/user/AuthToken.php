<?php

namespace PHP2\App\user;

use DateTimeInterface;

class AuthToken
{
    private string $token;
    private User $user;
    private DateTimeInterface $expiresAt;

    public function __construct(string $token, User $user, DateTimeInterface $expiresAt)
    {
        $this->token = $token;
        $this->user = $user;
        $this->expiresAt = $expiresAt;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

}