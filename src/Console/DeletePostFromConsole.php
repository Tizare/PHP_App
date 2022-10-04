<?php

namespace PHP2\App\Console;

use Exception;
use PHP2\App\Argument\Argument;
use PHP2\App\Commands\DeletePostCommandInterface;
use PHP2\App\Repositories\PostRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePostFromConsole extends Command
{
    private DeletePostCommandInterface $deletePostCommand;
    private PostRepositoryInterface $postRepository;

    public function __construct(DeletePostCommandInterface $deletePostCommand, PostRepositoryInterface $postRepository)
    {
        parent::__construct();
        $this->deletePostCommand = $deletePostCommand;
        $this->postRepository = $postRepository;
    }

    protected function configure(): void
    {
        $this->setName('post:delete')
            ->setDescription("Delete post by Id")
            ->addArgument("postId", InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // исходим из того, что консолью у нас пользуется только админ, так что Id пользователя берём из базы
        // TODO: - исправить функцию, если к ней есть общий доступ!
        try {
            $postId = $input->getArgument('postId');
            $authUser = $this->postRepository->get($postId)->getUserId();

            $question = new ConfirmationQuestion("We find your post (id - $postId). 
            Are you sure you want to delete it with all comments?
            [Y/n] -> ");

            if(!$this->getHelper('question')->ask($input, $output, $question)) {
                $output->writeln("Delete canceled");
                return Command::SUCCESS;
            }

            $argument = new Argument(['postId' => $postId, 'authUser' => $authUser]);

            $this->deletePostCommand->handle($argument);

        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("Post $postId deleted with all comments and likes");
        return Command::SUCCESS;

    }

}