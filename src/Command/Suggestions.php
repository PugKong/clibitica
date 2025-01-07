<?php

declare(strict_types=1);

namespace App\Command;

use App\Habitica\Habitica;
use App\Habitica\Task\Daily;
use App\Habitica\Task\Todo;
use Closure;
use RuntimeException;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Webmozart\Assert\Assert;

use function sprintf;

final readonly class Suggestions implements InputMapper\Suggestions
{
    public const string TAG_ID = 'tagId';
    public const string TASK_ID = 'taskId';
    public const string CHECKLIST_TASK_ID = 'checklistTaskId';
    public const string CHECKLIST_ITEM_ID = 'checklistItemId';

    public function __construct(private Habitica $habitica)
    {
    }

    public function suggester(string $name): Closure
    {
        return match ($name) {
            self::TAG_ID => $this->tagId(...),
            self::TASK_ID => $this->taskId(...),
            self::CHECKLIST_TASK_ID => $this->checklistTaskId(...),
            self::CHECKLIST_ITEM_ID => $this->checklistItemId(...),
            default => throw new RuntimeException(sprintf('Unknown suggester: %s', $name)),
        };
    }

    public function reverseTagId(string $id): string
    {
        $response = $this->habitica->listTags();

        foreach ($response->data as $tag) {
            if ($this->humanId($tag->id, $tag->name) === $id) {
                return $tag->id;
            }
        }

        return $id;
    }

    public function reverseTaskId(string $id): string
    {
        $response = $this->habitica->listTasks();

        foreach ($response->data as $task) {
            if ($this->humanId($task->id, $task->text) === $id) {
                return $task->id;
            }
        }

        return $id;
    }

    public function reverseChecklistItemId(string $taskId, string $id): string
    {
        $response = $this->habitica->listTasks();

        foreach ($response->data as $task) {
            if (!($task instanceof Daily || $task instanceof Todo)) {
                continue;
            }

            if ($taskId !== $task->id) {
                continue;
            }

            foreach ($task->checklist as $item) {
                if ($this->humanId($item->id, $item->text) === $id) {
                    return $item->id;
                }
            }
        }

        return $id;
    }

    /**
     * @return list<Suggestion>
     */
    private function tagId(CompletionInput $input): array
    {
        $response = $this->habitica->listTags();

        $suggestions = [];
        foreach ($response->data as $tag) {
            $id = $tag->id;
            $title = $tag->name;
            $humanId = $this->humanId($id, $title);

            if ($this->contains($input->getCompletionValue(), $id, $title, $humanId)) {
                $suggestions[] = new Suggestion($humanId, $title);
            }
        }

        return $suggestions;
    }

    /**
     * @return list<Suggestion>
     */
    private function taskId(CompletionInput $input): array
    {
        $response = $this->habitica->listTasks();

        $suggestions = [];
        foreach ($response->data as $task) {
            $id = $task->id;
            $title = $task->text;
            $humanId = $this->humanId($id, $title);

            if ($this->contains($input->getCompletionValue(), $id, $title, $humanId)) {
                $suggestions[] = new Suggestion($humanId, $title);
            }
        }

        return $suggestions;
    }

    /**
     * @return list<Suggestion>
     */
    private function checklistTaskId(CompletionInput $input): array
    {
        $response = $this->habitica->listTasks();

        $suggestions = [];
        foreach ($response->data as $task) {
            if (!($task instanceof Daily || $task instanceof Todo)) {
                continue;
            }

            $id = $task->id;
            $title = $task->text;
            $humanId = $this->humanId($id, $title);

            if ($this->contains($input->getCompletionValue(), $id, $title, $humanId)) {
                $suggestions[] = new Suggestion($humanId, $title);
            }
        }

        return $suggestions;
    }

    /**
     * @return list<Suggestion>
     */
    private function checklistItemId(CompletionInput $input): array
    {
        Assert::string($taskId = $input->getArgument('id'));
        Assert::string($action = $input->getArgument('action'));

        if ('add' === $action) {
            return [];
        }

        $taskId = $this->reverseTaskId($taskId);
        $checklist = [];
        $tasks = $this->habitica->listTasks()->data;
        foreach ($tasks as $task) {
            if ($task->id === $taskId) {
                if ($task instanceof Daily || $task instanceof Todo) {
                    $checklist = $task->checklist;
                }

                break;
            }
        }

        $suggestions = [];
        foreach ($checklist as $item) {
            $id = $item->id;
            $title = $item->text;
            $humanId = $this->humanId($id, $title);

            if ($this->contains($input->getCompletionValue(), $id, $title, $humanId)) {
                $suggestions[] = new Suggestion($humanId, $title);
            }
        }

        return $suggestions;
    }

    private function humanId(string $id, string $title): string
    {
        return substr($id, 0, 4).'-'.strtolower(strtr($title, ' ', '-'));
    }

    private function contains(string $needle, string ...$subjects): bool
    {
        $needle = mb_strtolower($needle);

        foreach ($subjects as $subject) {
            if (str_contains(mb_strtolower($subject), $needle)) {
                return true;
            }
        }

        return false;
    }
}
