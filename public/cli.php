<?php

require_once __DIR__ . '\autoload_runtime.php';

use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\blog\Comment;
use PHP2\App\Commands\CreatePostCommand;
use PHP2\App\Commands\CreatePostLikeCommand;
use PHP2\App\Commands\CreateUserCommand;
use PHP2\App\Exceptions\CommandException;
use PHP2\App\Repositories\CommentRepository;
use PHP2\App\Repositories\PostRepository;
use PHP2\App\Repositories\UserRepository;
use PHP2\App\user\User;

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

$userRepository = new UserRepository();
$postRepository = new PostRepository();
//$commentRepository = new CommentRepository();
////$user = new User('Las', 'Vasy', 'Pupkin');
////$userRepository->save($user);
//
//$user = $userRepository->get(2);
//
////$post = new Post("Some news", "Bla-bla-bla and bla-bla and Bla-bla!");
////$postRepository->save($user, $post);
//
//$post = $postRepository->get(1);
//
//$comment = new Comment('Wow!');
//$commentRepository->save($user, $post, $comment);
//
//var_dump($comment);
//die();

$command = new CreateUserCommand($userRepository);
//$command2 = new CreatePostCommand($postRepository, $userRepository);
$command3 = new CreatePostLikeCommand($postRepository, $userRepository);

try {
    $command3->handle(Argument::fromArgv($argv));
    echo "done" . PHP_EOL;
} catch (CommandException $commandException) {
    echo $commandException->getMessage();
}
