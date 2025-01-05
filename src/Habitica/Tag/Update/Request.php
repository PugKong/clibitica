<?php

declare(strict_types=1);

namespace App\Habitica\Tag\Update;

use Symfony\Component\Serializer\Attribute\Ignore;

final readonly class Request
{
    public function __construct(
        #[Ignore]
        public string $id,
        ?string $name = null,
    ) {
        if (null !== $name) {
            $this->name = $name;
        }
    }

    public string $name;
}
