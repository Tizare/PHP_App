<?php

namespace PHP2\App\Handler\Users;

use PHP2\App\Exceptions\HttpException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;

class FindByUserName implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->userRepository->getUserByUsername($username);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->getName() . ' ' . $user->getSurname(),
        ]);
    }
}