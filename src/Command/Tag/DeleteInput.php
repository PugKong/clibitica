<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\InputMapper\Attribute\Argument;

final readonly class DeleteInput
{
    public function __construct(
        #[Argument('id', 'The tag id', 'tagId')]
        public string $id,
    ) {
    }
}
