<?php

namespace Test\Console;


use Monolog\Test\TestCase;
use PHP2\App\Console\CreateUserFromConsole;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Test\Traits\ContainerTrait;

class CreateUserFromConsoleTest extends TestCase
{
    use ContainerTrait;

    public function testItRequiresUsername(): void
    {
        $command = $this->getContainer()->get(CreateUserFromConsole::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "username")');

        $command->run(new ArrayInput([
            'surname' => 'surname',
            'name' => 'name',
            'password' => 'password'
        ]), new NullOutput());
    }

    public function testItRequiresName(): void
    {
        $command = $this->getContainer()->get(CreateUserFromConsole::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "name")');

        $command->run(new ArrayInput([
            'username' => 'username',
            'surname' => 'surname',
            'password' => 'password'
        ]), new NullOutput());
    }

    public function testItRequiresSurname(): void
    {
        $command = $this->getContainer()->get(CreateUserFromConsole::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "surname")');

        $command->run(new ArrayInput([
            'username' => 'username',
            'name' => 'name',
            'password' => 'password'
        ]), new NullOutput());
    }

    public function testItRequiresPassword(): void
    {
        $command = $this->getContainer()->get(CreateUserFromConsole::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "password")');

        $command->run(new ArrayInput([
            'username' => 'username',
            'name' => 'name',
            'surname' => 'surname'
        ]), new NullOutput());
    }


}