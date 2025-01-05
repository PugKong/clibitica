<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use App\Command\Suggestions;

final readonly class UpdateInput
{
    public function __construct(
        #[Argument('id', 'The tag id', Suggestions::TAG_ID)]
        public string $tag,
        #[Option('name', 'The new name of the tag')]
        public ?string $name = null,
    ) {
    }
}
