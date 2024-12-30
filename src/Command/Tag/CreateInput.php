<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\InputMapper\Attribute\Argument;

final readonly class CreateInput
{
    public function __construct(
        #[Argument('name', 'The name of the tag to be added')]
        public string $name,
    ) {
    }
}
