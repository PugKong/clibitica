<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputException;
use App\Command\InputMapper\Mapper;
use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Task\Checklist;
use App\Habitica\Task\ChecklistItem;
use App\Habitica\Task\Daily;
use App\Habitica\Task\Todo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

#[AsCommand(name: 'task:checklist', description: 'Manage checklist for a task')]
final class ChecklistCommand extends Command
{
    public function __construct(
        private readonly Mapper $mapper,
        private readonly Habitica $habitica,
        private readonly Suggestions $suggestions,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, ChecklistInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, ChecklistInput::class);

        $taskId = $this->suggestions->reverseTaskId($data->task);
        $textOrItemId = $data->itemTextOrId;
        if (ChecklistAction::ADD !== $data->action) {
            $textOrItemId = $this->suggestions->reverseChecklistItemId($taskId, $data->itemTextOrId);
        }

        match ($data->action) {
            ChecklistAction::ADD => $this->add($taskId, $textOrItemId),
            ChecklistAction::DELETE => $this->delete($taskId, $textOrItemId),
            ChecklistAction::TOGGLE => $this->toggle($taskId, $textOrItemId),
        };

        return self::SUCCESS;
    }

    private function add(string $taskId, string $textId): void
    {
        $this->habitica->addChecklistItem(new Checklist\Add\Request(
            task: $taskId,
            text: $textId,
        ));
    }

    private function delete(string $taskId, string $itemId): void
    {
        $this->habitica->deleteChecklistItem(new Checklist\Delete\Request(
            task: $taskId,
            item: $itemId,
        ));
    }

    private function toggle(string $taskId, string $itemId): void
    {
        $task = (function (string $taskId): Daily|Todo|null {
            foreach ($this->habitica->listTasks()->data as $task) {
                if (!($task instanceof Daily || $task instanceof Todo)) {
                    continue;
                }

                if ($taskId === $task->id) {
                    return $task;
                }
            }

            return null;
        })($taskId);

        if (null === $task) {
            throw new InputException(sprintf('Task "%s" not found', $taskId));
        }

        $item = (function (Daily|Todo $task, string $itemId): ?ChecklistItem {
            foreach ($task->checklist as $item) {
                if ($itemId === $item->id) {
                    return $item;
                }
            }

            return null;
        })($task, $itemId);

        if (null === $item) {
            throw new InputException(sprintf('Checklist item "%s" not found', $itemId));
        }

        $this->habitica->updateChecklistItem(new Checklist\Update\Request(
            task: $taskId,
            item: $itemId,
            completed: !$item->completed,
        ));
    }
}
