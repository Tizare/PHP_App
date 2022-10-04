<?php

namespace PHP2\App\Console;

use Exception;
use PHP2\App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FindUserFromConsole extends Command
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setName('user:find')
            ->setDescription('Find user by Username or Id (option)')
            ->addArgument('argument', InputArgument::REQUIRED)
            ->addOption('id', 'id', InputOption::VALUE_NONE, 'Find user by Id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if($input->getOption('id')) {
                $id = $input->getArgument('argument');
                $user = $this->userRepository->get($id);
            } else {
                $username = $input->getArgument('argument');
                $user = $this->userRepository->getUserByUsername($username);
            }

            $id = $user->getId();
            $username = $user->getUsername();
            $name = $user->getName();
            $surname = $user->getSurname();
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("We found your user! Here it is:
        userId: $id, username: $username, name: $name, surname: $surname");
        return Command::SUCCESS;
    }

}