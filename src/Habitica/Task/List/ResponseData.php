<?php

declare(strict_types=1);

namespace App\Habitica\Task\List;

use App\Habitica\Task\Type;
use DateTimeInterface;

final readonly class ResponseData
{
    public function __construct(
        public string $id,
        public string $text,
        public Type $type,
        public int|float $priority,
        public ?DateTimeInterface $date = null,
        /** @var string[] */
        public array $tags = [],
        public ?bool $isDue = null,
        public ?bool $completed = null,
    ) {
    }
}
