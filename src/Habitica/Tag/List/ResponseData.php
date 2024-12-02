<?php

declare(strict_types=1);

namespace App\Habitica\Tag\List;

final readonly class ResponseData
{
    public function __construct(public string $id, public string $name)
    {
    }
}
