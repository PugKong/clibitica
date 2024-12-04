<?php

declare(strict_types=1);

namespace App\Command;

use App\Habitica\Task\Priority;

enum TaskDifficulty: string
{
    case TRIVIAL = 'trivial';
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function toPriority(): int|float
    {
        return match ($this) {
            self::TRIVIAL => Priority::TRIVIAL,
            self::EASY => Priority::EASY,
            self::MEDIUM => Priority::MEDIUM,
            self::HARD => Priority::HARD,
        };
    }
}
