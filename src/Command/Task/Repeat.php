<?php

declare(strict_types=1);

namespace App\Command\Task;

enum Repeat: string
{
    case SUNDAY = 'su';
    case MONDAY = 'mo';
    case TUESDAY = 'tu';
    case WEDNESDAY = 'we';
    case THURSDAY = 'th';
    case FRIDAY = 'fr';
    case SATURDAY = 'sa';
}
