<?php

declare(strict_types=1);

namespace App\Command\Task;

enum TagAction: string
{
    case ADD = 'add';
    case DELETE = 'delete';
}
