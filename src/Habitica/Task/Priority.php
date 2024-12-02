<?php

declare(strict_types=1);

namespace App\Habitica\Task;

enum Priority: string
{
    case TRIVIAL = '0.1';
    case EASY = '1';
    case MEDIUM = '1.5';
    case HARD = '2';
}
