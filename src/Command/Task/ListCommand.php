<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Suggestions;
use App\Command\TaskDifficulty;
use App\Habitica\Habitica;
use App\Habitica\Tag;
use App\Habitica\Task;
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
        $tasks = $this->filterTasks($input, $this->habitica->listTasks()->data);
        $tags = $this->makeTagsMap($this->habitica->listTags()->data);

        $headers = ['id', 'type', 'difficulty', 'due', 'tags', 'text'];
        $rows = [];
        foreach ($tasks as $task) {
            $rows[] = $this->makeRow($tags, $task);
        }

        $io = new SymfonyStyle($input, $output);
        $io->table($headers, $rows);

        return self::SUCCESS;
    }

    /**
     * @param Tag\List\ResponseData[] $tags
     *
     * @return array<string, string>
     */
    private function makeTagsMap(array $tags): array
    {
        $result = [];
        foreach ($tags as $tag) {
            $result[$tag->id] = $tag->name;
        }

        return $result;
    }

    /**
     * @param Task\List\ResponseData[] $tasks
     *
     * @return Task\List\ResponseData[]
     */
    private function filterTasks(InputInterface $input, array $tasks): array
    {
        Assert::nullOrString($type = $input->getOption('type'));
        Assert::boolean($all = $input->getOption('all'));

        if (null !== $type) {
            $type = Task\Type::from($type);
        }

        $result = [];
        foreach ($tasks as $task) {
            if (Task\Type::REWARD === $type && Task\Type::REWARD === $task->type) {
                $result[] = $task;

                continue;
            }

            if (null !== $type && $type !== $task->type) {
                continue;
            }

            if (!$all && Task\Type::REWARD === $task->type) {
                continue;
            }

            if (!$all && Task\Type::DAILY === $task->type && ($task->completed || false === $task->isDue)) {
                continue;
            }

            $result[] = $task;
        }

        return $result;
    }

    /**
     * @param array<string, string> $tags
     *
     * @return mixed[]
     */
    private function makeRow(array $tags, Task\List\ResponseData $task): array
    {
        $row = [];

        $row[] = substr($task->id, 0, 8);
        $row[] = $task->type->value;
        $row[] = TaskDifficulty::fromPriority($task->priority)->value;
        $row[] = $task->date?->format('Y-m-d');
        $row[] = implode(', ', array_map(fn (string $id) => $tags[$id] ?? $id, $task->tags));

        $text = $task->text;
        if (Task\Type::HABIT === $task->type) {
            $text .= " (up: $task->counterUp; down: $task->counterDown)";
        } elseif (Task\Type::DAILY === $task->type) {
            $text .= " (streak: $task->streak)";
        }
        $row[] = $text;

        return $row;
    }
}
