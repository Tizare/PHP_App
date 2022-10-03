<?php

namespace PHP2\App\Console;

use Exception;
use PHP2\App\Argument\Argument;
use PHP2\App\Commands\DeleteCommentCommandInterface;
use PHP2\App\Repositories\CommentRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteCommentFromConsole extends Command
{
    private DeleteCommentCommandInterface $deleteCommentCommand;
    private CommentRepositoryInterface $commentRepository;

    public function __construct(DeleteCommentCommandInterface $deleteCommentCommand, CommentRepositoryInterface $commentRepository)
    {
        parent::__construct();
        $this->deleteCommentCommand = $deleteCommentCommand;
        $this->commentRepository = $commentRepository;
    }

    protected function configure(): void
    {
        $this->setName('comment:delete')
            ->setDescription("Delete comment by Id")
            ->addArgument('commentId', InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // исходим из того, что консолью у нас пользуется только админ, так что Id пользователя берём из базы
        // TODO: - исправить функцию, если к ней есть общий доступ!
        try {
            $commentId = $input->getArgument('commentId');
            $authUser = $this->commentRepository->get($commentId)->getUserId();

            $question = new ConfirmationQuestion("We find your comment (id - $commentId).
            Are you sure you want to delete it?
            [Y/n] -> ");

            if(!$this->getHelper('question')->ask($input, $output, $question)) {
                $output->writeln("Delete canceled");
                return Command::SUCCESS;
            }

            $argument = new Argument(['commentId' => $commentId, 'authUser' => $authUser]);

            $this->deleteCommentCommand->handle($argument);
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("Comment $commentId deleted.");
        return Command::SUCCESS;
    }

}