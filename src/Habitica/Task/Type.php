<?php

declare(strict_types=1);

namespace App\Habitica\Task;

enum Type: string
{
    case HABIT = 'habit';
    case DAILY = 'daily';
    case TODO = 'todo';
    case REWARD = 'reward';
}
