<?php

namespace PHP2\App\Console;

use Exception;
use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreatePostCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreatePostFromConsole extends Command
{
    private CreatePostCommandInterface $createPostCommand;

    public function __construct(CreatePostCommandInterface $createPostCommand)
    {
        parent::__construct();
        $this->createPostCommand = $createPostCommand;
    }

    protected function configure(): void
    {
        $this->setName('post:create')
            ->setDescription("Create new post")
            ->addArgument('authUser', InputArgument::REQUIRED)
            ->addArgument('title', InputArgument::REQUIRED)
            ->addArgument('post', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Create post command start');

        try {
            $authUser = $input->getArgument('authUser');
            $title = $input->getArgument('title');
            $post = $input->getArgument('post');

            $argument = new Argument([
                'authUser' => $authUser,
                'title' => $title,
                'post' => $post
            ]);

            $question = new ConfirmationQuestion(
                "Are you sure you want to create this post:
                userId - $authUser, title - '$title'?
                [Y/n] -> "
            );

            if(!$this->getHelper('question')->ask($input, $output, $question)) {
                $output->writeln("Creation post canceled");
                return Command::SUCCESS;
            }

            $this->createPostCommand->handle($argument);
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("Post '$title' created");
        return Command::SUCCESS;
    }

}