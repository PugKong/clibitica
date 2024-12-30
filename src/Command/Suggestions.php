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

    /**
     * @return list<Suggestion>
     */
    private function tagId(CompletionInput $input): array
    {
        $response = $this->habitica->listTags();

        $suggestions = [];
        foreach ($response->data as $tag) {
            $needle = mb_strtolower($input->getCompletionValue());
            if (str_contains(mb_strtolower($tag->id), $needle) || str_contains(mb_strtolower($tag->name), $needle)) {
                $suggestions[] = new Suggestion($tag->id, $tag->name);
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
            $needle = mb_strtolower($input->getCompletionValue());
            if (str_contains(mb_strtolower($task->id), $needle) || str_contains(mb_strtolower($task->text), $needle)) {
                $suggestions[] = new Suggestion($task->id, $task->text);
            }
        }

        return $suggestions;
    }
}
