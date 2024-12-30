<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Habitica\Habitica;
use App\Habitica\Tag;
use App\Habitica\Task;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('task:list', description: 'List tasks')]
final class ListCommand extends Command
{
    public function __construct(private readonly Mapper $mapper, private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, ListInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
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
     * @param Task\Item[] $tasks
     *
     * @return Task\Item[]
     */
    private function filterTasks(InputInterface $input, array $tasks): array
    {
        $data = $this->mapper->map($input, ListInput::class);

        $result = [];
        foreach ($tasks as $task) {
            if (Task\Type::REWARD === $data->type && Task\Type::REWARD === $task->type) {
                $result[] = $task;

                continue;
            }

            if (null !== $data->type && $data->type !== $task->type) {
                continue;
            }

            if (!$data->all && Task\Type::REWARD === $task->type) {
                continue;
            }

            if (!$data->all && $task instanceof Task\Daily && ($task->completed || false === $task->isDue)) {
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
    private function makeRow(array $tags, Task\Item $task): array
    {
        $row = [];

        $row[] = substr($task->id, 0, 8);
        $row[] = $task->type->value;
        $row[] = $task instanceof Task\Task ? $task->difficulty->value : null;
        $row[] = $task instanceof Task\Todo ? $task->date?->format('Y-m-d') : null;
        $row[] = implode(', ', array_map(fn (string $id) => $tags[$id] ?? $id, $task->tags));
        $row[] = $task->text.match ($task::class) {
            Task\Habit::class => " (up: $task->counterUp; down: $task->counterDown)",
            Task\Daily::class => " (streak: $task->streak)",
            default => '',
        };

        return $row;
    }
}
