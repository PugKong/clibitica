<?php

declare(strict_types=1);

namespace App\Habitica\Task;

enum Attribute: string
{
    case STR = 'str';
    case INT = 'int';
    case PER = 'per';
    case CON = 'con';
}
