<?php

declare(strict_types=1);

namespace App\Habitica\Tag\Create;

final readonly class Request
{
    public function __construct(public string $name)
    {
    }
}
