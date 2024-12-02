<?php

declare(strict_types=1);

namespace App\Habitica\Tag\Create;

final readonly class ResponseData
{
    public function __construct(public string $id)
    {
    }
}
