<?php

namespace PHP2\App\Console;

use Exception;
use PHP2\App\Argument\Argument;
use PHP2\App\Commands\CreateUserCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateUserFromConsole extends Command
{
    private CreateUserCommandInterface $createUserCommand;

    public function __construct(CreateUserCommandInterface $createUserCommand)
    {
        parent::__construct();
        $this->createUserCommand = $createUserCommand;
    }

    protected function configure(): void
    {
        $this->setName('user:create')
            ->setDescription('Create new user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('surname', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Create user command start');

        try {
            $username = $input->getArgument('username');
            $name = $input->getArgument('name');
            $surname = $input->getArgument('surname');
            $password = $input->getArgument('password');

            $argument = new Argument([
                'username' => $username,
                'name' => $name,
                'surname' => $surname,
                'password' => $password
            ]);

            $question = new ConfirmationQuestion(
                "Are you sure you want to create this user: 
                username - $username, name - $name, surname - $surname, password - $password? 
                [Y/n] -> ",
                false
            );

            if(!$this->getHelper('question')->ask($input, $output, $question)) {
                $output->writeln('Creation user canceled');
                return Command::SUCCESS;
            }

            $this->createUserCommand->handle($argument);
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("User $username created");
        return Command::SUCCESS;
    }

}