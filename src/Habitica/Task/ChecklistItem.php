<?php

declare(strict_types=1);

namespace App\Habitica\Task;

final readonly class ChecklistItem
{
    public function __construct(
        public string $id,
        public string $text,
        public bool $completed,
    ) {
    }
}
