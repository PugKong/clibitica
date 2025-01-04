<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\Suggestions;

final readonly class TagInput
{
    public function __construct(
        #[Argument('action', 'Action "add" or "delete"')]
        public TagAction $action,
        #[Argument('task', 'The task id or alias', Suggestions::TASK_ID)]
        public string $task,
        #[Argument('tag', 'The tag id', Suggestions::TAG_ID)]
        public string $tag,
    ) {
    }
}
