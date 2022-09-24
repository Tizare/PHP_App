<?php

namespace Test\Handler;

use JsonException;
use PHP2\App\Exceptions\UserNotFoundException;
use PHP2\App\Handler\Users\FindByUserName;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\Request\Request;
use PHP2\App\Response\ErrorResponse;
use PHP2\App\Response\SuccessfulResponse;
use PHP2\App\user\User;
use PHPUnit\Framework\TestCase;
use Test\DummyLogger;

class FindByUserNameHandlerTest extends TestCase
{
    private function usersRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface {

            private array $users;

            public function __construct(array $users)
            {
                $this->users = $users;
            }


            public function get(int $id): User
            {
                //
            }

            public function getUserByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getUsername()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("Such user not found");
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        $request = new Request([], [], '');

        $usersRepository = $this->usersRepository([]);
        $dummyLogger = new DummyLogger();

        $action = new FindByUserName($usersRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');

        $usersRepository = $this->usersRepository([]);
        $dummyLogger = new DummyLogger();

        $action = new FindByUserName($usersRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Such user not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');

        $usersRepository = $this->usersRepository([new User('ivan', 'Ivan', 'Nikitin', 'password')]);
        $dummyLogger = new DummyLogger();

        $action = new FindByUserName($usersRepository, $dummyLogger);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString(
            '{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');

        $response->send();
    }

}