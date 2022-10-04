<?php

namespace PHP2\App\Console;

use Faker\Generator;
use PHP2\App\Argument\Argument;
use PHP2\App\blog\Post;
use PHP2\App\Commands\CreateCommentCommandInterface;
use PHP2\App\Commands\CreatePostCommandInterface;
use PHP2\App\Commands\CreateUserCommandInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use PHP2\App\Repositories\UserRepositoryInterface;
use PHP2\App\user\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class MassFillDatabaseFromConsole extends Command
{
    private CreateUserCommandInterface $createUserCommand;
    private UserRepositoryInterface $userRepository;
    private CreatePostCommandInterface $createPostCommand;
    private PostRepositoryInterface $postRepository;
    private CreateCommentCommandInterface $createCommentCommand;
    private Generator $faker;

    public function __construct(CreateUserCommandInterface $createUserCommand, UserRepositoryInterface $userRepository,
                                CreatePostCommandInterface $createPostCommand, PostRepositoryInterface $postRepository,
                                CreateCommentCommandInterface $createCommentCommand, Generator $faker)
    {
        parent::__construct();
        $this->createUserCommand = $createUserCommand;
        $this->userRepository = $userRepository;
        $this->createPostCommand = $createPostCommand;
        $this->postRepository = $postRepository;
        $this->createCommentCommand = $createCommentCommand;
        $this->faker = $faker;
    }

    protected function configure(): void
    {
        $this->setName('faker:fill-db')
            ->setDescription('Fill database with fake users, posts and comments for tests (10/10/100 default)')
            ->addOption('count', 'ct', InputOption::VALUE_OPTIONAL,
                'Count of users you want to create.
                !BE CAREFUL Every user will create a post and make a comment to each post!')
            ->addOption('count-post', 'ctp', InputOption::VALUE_OPTIONAL,
                "Count of posts you want to create.
                !BE CAREFUL Every post will get a comment from each user!");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = [];
        $posts = [];
        $count = $input->getOption('count') ?: 10;
        $postCount = $input->getOption('count-post') ?: 1;
        $io = new SymfonyStyle($input, $output);
        $question = new ConfirmationQuestion(
            "Are you sure you want to fill your base with $count users? 
            Each one will write a post (in quantity $postCount) and make a comment to each post.
            [Y/n] -> "
        );

        if(!$this->getHelper('question')->ask($input, $output, $question)) {
            $output->writeln("Fill the base canceled");
            return Command::SUCCESS;
        }

        $output->writeln('We start to fill your base with fake data.');
        $io->progressStart($count + $count * $postCount + $count * $postCount * $count);

        $output->writeln(" -> We imagining users.");
        for ($i = 0; $i < $count; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $io->progressAdvance();
        }

        $output->writeln(" -> We creating interesting posts from your users.");
        foreach ($users as $user) {
            for ($i = 0; $i < $postCount; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $io->progressAdvance();
                $output->writeln(
                    " -> User ({$user->getUsername()} Id {$user->getId()}) created and write post '{$post->getTitle()}'"
                );
            }
        }

        $output->writeln("We begin to commenting everything. Wait a minute.");

        foreach ($posts as $post) {
            foreach ($users as $user) {
                $this->createFakeComment($user, $post);
                $io->progressAdvance();
            }
        }

        $io->progressFinish();
        $output->writeln("Filling finished");

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $username = $this->faker->userName();
        $argument = new Argument([
            'username' => $username,
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'password' => $this->faker->password()
        ]);

        $this->createUserCommand->handle($argument);

        return $this->userRepository->getUserByUsername($username);
    }

    private function createFakePost(User $user): Post
    {
        $userId = $user->getId();
        $title = $this->faker->realText($maxNbChars = 30, $indexSize = 2);
        $argument = new Argument([
            'authUser' => $userId,
            'title' => $title,
            'post' => $this->faker->realText()
        ]);

        $this->createPostCommand->handle($argument);
        return $this->postRepository->findPost($userId, $title);
    }

    private function createFakeComment(User $user, Post $post): void
    {
        $argument = new Argument([
            'authUser' => $user->getId(),
            'postId' => $post->getId(),
            'comment' => $this->faker->realText($maxNbChars = 100, $indexSize = 2)
        ]);

        $this->createCommentCommand->handle($argument);
    }

}