<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\Suggestions;

final readonly class InfoInput
{
    public function __construct(
        #[Argument('id', 'The task id or alias', Suggestions::TASK_ID)]
        public string $task,
    ) {
    }
}
