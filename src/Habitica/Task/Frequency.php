<?php

declare(strict_types=1);

namespace App\Habitica\Task;

enum Frequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
