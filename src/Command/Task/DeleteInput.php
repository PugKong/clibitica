<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;

final readonly class DeleteInput
{
    public function __construct(
        #[Argument('id', 'The task id or alias', 'taskId')]
        public string $task,
    ) {
    }
}
