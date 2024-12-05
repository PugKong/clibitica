<?php

declare(strict_types=1);

namespace App\Habitica\Task;

final readonly class Priority
{
    public const float TRIVIAL = 0.1;
    public const int EASY = 1;
    public const float MEDIUM = 1.5;
    public const int HARD = 2;
}
