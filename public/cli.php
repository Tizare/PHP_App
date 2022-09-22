<?php

$container = require_once __DIR__ . '\autoload_runtime.php';

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Exceptions\CommandException;
use Psr\Log\LoggerInterface;

//$faker = Faker\Factory::create();
//
//if (count($argv) > 1) {
//    switch ($argv[1]) {
//        case "user":
//            echo new User($faker->userName(), $faker->firstName(), $faker->lastName());
//            break;
//        case "blog":
//            echo new Post($faker->city(), $faker->text());
//            break;
//        case "comment":
//            echo new Comment($faker->text());
//            break;
//    }
//}

$command = $container->get(CreateUserCommand::class);
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Argument::fromArgv($argv));
    echo "done" . PHP_EOL;
} catch (CommandException $commandException) {
    $logger->error($commandException->getMessage(), ['exception' => $commandException]);
}

