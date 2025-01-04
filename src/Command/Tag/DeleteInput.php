<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\Suggestions;

final readonly class DeleteInput
{
    public function __construct(
        #[Argument('id', 'The tag id', Suggestions::TAG_ID)]
        public string $id,
    ) {
    }
}
