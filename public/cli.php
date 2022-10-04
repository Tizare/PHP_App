<?php

$container = require_once __DIR__ . '\autoload_runtime.php';

use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Console\CreatePostFromConsole;
use PHP2\App\Console\CreateUserFromConsole;
use PHP2\App\Console\DeleteCommentFromConsole;
use PHP2\App\Console\DeletePostFromConsole;
use PHP2\App\Console\FindUserFromConsole;
use PHP2\App\Console\MassFillDatabaseFromConsole;
use PHP2\App\Exceptions\CommandException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

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

//$command = $container->get(CreateUserCommand::class);
//$logger = $container->get(LoggerInterface::class);
//
//try {
//    $command->handle(Argument::fromArgv($argv));
//    echo "done" . PHP_EOL;
//} catch (CommandException $commandException) {
//    $logger->error($commandException->getMessage(), ['exception' => $commandException]);
//}

$application = new Application();
$commandClasses = [
    CreateUserFromConsole::class,
    FindUserFromConsole::class,
    CreatePostFromConsole::class,
    MassFillDatabaseFromConsole::class,
    DeletePostFromConsole::class,
    DeleteCommentFromConsole::class
];

foreach ($commandClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();

