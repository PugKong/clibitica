<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Option;
use App\Habitica\Task\Type;

final readonly class ListInput
{
    public function __construct(
        #[Option('type', 'Task type, options are: "habit", "daily", "todo", "reward"')]
        public ?Type $type = null,
        #[Option('all', 'Include rewards and not due daily task')]
        public bool $all = false,
    ) {
    }
}
