<?php

namespace PHP2\App\Handler\Users;

use Exception;
use PHP2\App\Authentication\PasswordAuthentication;
use PHP2\App\Commands\CreateAuthTokenCommandInterface;
use PHP2\App\Exceptions\AuthException;
use PHP2\App\Handler\HandlerInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\Response;
use PHP2\App\Response\SuccessfulResponse;
use PHP2\App\user\AuthToken;

class LoginHandle implements HandlerInterface
{
    private PasswordAuthentication $passwordAuthentication;
    private CreateAuthTokenCommandInterface $createAuthTokenCommand;

    public function __construct(PasswordAuthentication $passwordAuthentication, CreateAuthTokenCommandInterface $createAuthTokenCommand)
    {
        $this->passwordAuthentication = $passwordAuthentication;
        $this->createAuthTokenCommand = $createAuthTokenCommand;
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user,
            (new \DateTimeImmutable())->modify('+1 day')
        );

        $this->createAuthTokenCommand->handle($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }
}