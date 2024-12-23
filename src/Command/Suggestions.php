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
        return array_map(fn (Task\Difficulty $difficulty) => $difficulty->value, Task\Difficulty::cases());
    }

    /**
     * @return list<string>
     */
    public function attribute(): array
    {
        return array_map(fn (Task\Attribute $attribute) => $attribute->value, Task\Attribute::cases());
    }

    /**
     * @return list<string>
     */
    public function frequency(): array
    {
        return array_map(fn (Task\Frequency $frequency) => $frequency->value, Task\Frequency::cases());
    }

    /**
     * @return list<Suggestion>
     */
    public function tagId(CompletionInput $input): array
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
    public function taskId(CompletionInput $input): array
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
