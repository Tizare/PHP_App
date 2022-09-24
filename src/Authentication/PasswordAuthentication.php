<?php

namespace PHP2\App\Authentication;

use PHP2\App\Exceptions\AuthException;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\user\User;
use Psr\Log\LoggerInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    private UserRepositoryInterface $userRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        $this->logger->info("Begin Authentication by password");
        try {
            $username = $request->jsonBodyField('username');
            $password = $request->jsonBodyField('password');
        } catch (HttpException $exception) {
            $this->logger->error($exception->getMessage());
            throw new AuthException($exception->getMessage());
        }

        try {
            $user = $this->userRepository->getUserByUsername($username);
        } catch (UserNotFoundException $exception) {
            $this->logger->warning($exception->getMessage());
            throw new AuthException($exception->getMessage());
        }

        if (!$user->checkPassword($password)) {
            $this->logger->info("End Auth: password was not same");
            throw new AuthException("Wrong password $password");
        }

        $this->logger->info("End Auth: auth passed");

        return $user;
    }
}