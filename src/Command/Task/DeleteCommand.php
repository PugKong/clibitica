<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Suggestions;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'task:delete', description: 'Delete a task')]
final class DeleteCommand extends Command
{
    public function __construct(private readonly Habitica $habitica, private readonly Suggestions $suggestions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            name: 'id',
            mode: InputArgument::REQUIRED,
            description: 'The task id or alias',
            suggestedValues: $this->suggestions->taskId(...),
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::string($id = $input->getArgument('id'));

        $this->habitica->deleteTask($id);

        return self::SUCCESS;
    }
}
