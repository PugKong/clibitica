<?php

declare(strict_types=1);

namespace App\Habitica\Task\Checklist\Update;

use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Context([AbstractObjectNormalizer::SKIP_NULL_VALUES => true])]
final readonly class Request
{
    public function __construct(
        #[Ignore]
        public string $task,
        #[Ignore]
        public string $item,
        public ?string $text = null,
        public ?bool $completed = null,
    ) {
    }
}
