<?php

declare(strict_types=1);

namespace App\Habitica\Task;

enum Difficulty: string
{
    case TRIVIAL = 'trivial';
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
}
