<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;
use App\Habitica\Task\ScoreDirection;

final readonly class ScoreInput
{
    public function __construct(
        #[Argument('direction', 'Score direction: "up" or "down"')]
        public ScoreDirection $direction,
        #[Argument('id', 'The task id or alias', 'taskId')]
        public string $task,
    ) {
    }
}
