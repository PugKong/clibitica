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

#[AsCommand(name: 'task:tag:add', description: 'Add a tag to a task')]
final class TagAddCommand extends Command
{
    public function __construct(private readonly Habitica $habitica, private readonly Suggestions $suggestions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            name: 'task',
            mode: InputArgument::REQUIRED,
            description: 'The task id or alias',
            suggestedValues: $this->suggestions->taskId(...),
        );

        $this->addArgument(
            name: 'tag',
            mode: InputArgument::REQUIRED,
            description: 'The tag id',
            suggestedValues: $this->suggestions->tagId(...),
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::string($taskId = $input->getArgument('task'));
        Assert::string($tagId = $input->getArgument('tag'));

        $this->habitica->addTagToTask($taskId, $tagId);

        return self::SUCCESS;
    }
}