<?php

namespace PHP2\App\Handler\Users;

use DateTime;
use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindByUserName implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function handle(Request $request): Response
    {
        $this->logger->debug("Search user by username begin " . (new DateTime())->format('d.m.Y H:i:s'));

        try {
            $username = $request->query('username');
        } catch (HttpException $e) {
            $this->logger->error($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->userRepository->getUserByUsername($username);
        } catch (UserNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->debug("Search user by username end " . (new DateTime())->format('d.m.Y H:i:s'));
        $this->logger->info("User found: " . $user->getUsername());

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->getName() . ' ' . $user->getSurname(),
        ]);


    }
}