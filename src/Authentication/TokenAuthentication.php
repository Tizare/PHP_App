<?php

namespace PHP2\App\Authentication;

use DateTimeImmutable;
use InvalidArgumentException;
use PHP2\App\Exceptions\AuthException;
use PHP2\App\Exceptions\AuthTokenNotFoundException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Repositories\AuthTokenRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\user\User;

class TokenAuthentication implements TokenAuthenticationInterface
{
    private const PREFIX = 'Bearer ';
    private AuthTokenRepositoryInterface $authTokenRepository;


    public function __construct(AuthTokenRepositoryInterface $authTokenRepository)
    {
        $this->authTokenRepository = $authTokenRepository;
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException|InvalidArgumentException $exception) {
            throw new AuthException($exception->getMessage());
        }

        if ((stripos($header, self::PREFIX)) !== 0) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::PREFIX));

        try {
            $authToken = $this->authTokenRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->getExpiresAt() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        return $authToken->getUser();
    }

}