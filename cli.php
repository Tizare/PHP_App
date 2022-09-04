<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHP2\App\blog\Blog;
use PHP2\App\blog\Comment;
use PHP2\App\user\User;

$faker = Faker\Factory::create();

if (count($argv) > 1) {
    switch ($argv[1]) {
        case "user":
            echo new User($faker->firstName(), $faker->lastName());
            break;
        case "post":
            echo new Blog($faker->city(), $faker->text());
            break;
        case "comment":
            echo new Comment($faker->text());
            break;
    }
}