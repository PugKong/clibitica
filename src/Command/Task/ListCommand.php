<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Suggestions;
use App\Command\TaskDifficulty;
use App\Habitica\Habitica;
use App\Habitica\Task\Type;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

#[AsCommand('task:list', description: 'List tasks')]
final class ListCommand extends Command
{
    public function __construct(private readonly Habitica $habitica, private readonly Suggestions $suggestions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            name: 'type',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Task type, options are: "habit", "daily", "todo", "reward"',
            suggestedValues: $this->suggestions->taskType(),
        );

        $this->addOption(
            name: 'all',
            mode: InputOption::VALUE_NONE,
            description: 'Include rewards and not due daily task',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::nullOrString($type = $input->getOption('type'));
        Assert::boolean($all = $input->getOption('all'));

        if (null !== $type) {
            $type = Type::from($type);
        }

        $tags = [];
        foreach ($this->habitica->listTags()->data as $tag) {
            $tags[$tag->id] = $tag->name;
        }

        $tasks = [];
        foreach ($this->habitica->listTasks()->data as $task) {
            if (Type::REWARD === $type && Type::REWARD === $task->type) {
                $tasks[] = $task;

                continue;
            }

            if (null !== $type && $type !== $task->type) {
                continue;
            }

            if (!$all && Type::REWARD === $task->type) {
                continue;
            }

            if (!$all && Type::DAILY === $task->type && ($task->completed || false === $task->isDue)) {
                continue;
            }

            $tasks[] = $task;
        }

        $headers = ['id', 'type', 'difficulty', 'due', 'tags', 'text'];
        $rows = [];
        foreach ($tasks as $task) {
            $row = [];

            $row[] = substr($task->id, 0, 8);
            $row[] = $task->type->value;
            $row[] = TaskDifficulty::fromPriority($task->priority)->value;
            $row[] = $task->date?->format('Y-m-d');
            $row[] = implode(', ', array_map(fn (string $id) => $tags[$id] ?? $id, $task->tags));
            $row[] = $task->text;

            $rows[] = $row;
        }

        $io = new SymfonyStyle($input, $output);
        $io->table($headers, $rows);

        return self::SUCCESS;
    }
}
