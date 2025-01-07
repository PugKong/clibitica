<?php

declare(strict_types=1);

namespace App\Habitica\Task\Checklist\Add;

use Symfony\Component\Serializer\Attribute\Ignore;

final readonly class Request
{
    public function __construct(
        #[Ignore]
        public string $task,
        public string $text,
    ) {
    }
}
