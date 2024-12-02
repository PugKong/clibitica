<?php

declare(strict_types=1);

namespace App\Command;

use App\Habitica\Habitica;
use App\Habitica\Task;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;

final readonly class Suggestions
{
    public function __construct(private Habitica $habitica)
    {
    }

    /**
     * @return list<string>
     */
    public function taskType(): array
    {
        return array_map(fn (Task\Type $type) => $type->value, Task\Type::cases());
    }

    /**
     * @return list<string>
     */
    public function taskDifficulty(): array
    {
        return array_map(fn (TaskDifficulty $difficulty) => $difficulty->value, TaskDifficulty::cases());
    }

    /**
     * @return list<Suggestion>
     */
    public function tagId(CompletionInput $input): array
    {
        $response = $this->habitica->listTags();

        $suggestions = [];
        foreach ($response->data as $item) {
            $haystack = mb_strtolower($item->name);
            $needle = mb_strtolower($input->getCompletionValue());

            if (str_contains($haystack, $needle)) {
                $suggestions[] = new Suggestion($item->id, $item->name);
            }
        }

        return $suggestions;
    }

    /**
     * @return list<Suggestion>
     */
    public function taskId(CompletionInput $input): array
    {
        $response = $this->habitica->listTasks();

        $suggestions = [];
        foreach ($response->data as $item) {
            $haystack = mb_strtolower($item->text);
            $needle = mb_strtolower($input->getCompletionValue());

            if (str_contains($haystack, $needle)) {
                $suggestions[] = new Suggestion($item->id, $item->text);
            }
        }

        return $suggestions;
    }
}
