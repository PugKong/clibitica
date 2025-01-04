<?php

declare(strict_types=1);

namespace App\Command;

use App\Habitica\Habitica;
use Closure;
use RuntimeException;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;

use function sprintf;

final readonly class Suggestions implements InputMapper\Suggestions
{
    public function __construct(private Habitica $habitica)
    {
    }

    public function suggester(string $name): Closure
    {
        return match ($name) {
            'tagId' => $this->tagId(...),
            'taskId' => $this->taskId(...),
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
