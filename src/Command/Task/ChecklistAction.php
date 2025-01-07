<?php

declare(strict_types=1);

namespace App\Command\Task;

enum ChecklistAction: string
{
    case ADD = 'add';
    case DELETE = 'delete';
    case TOGGLE = 'toggle';
}
