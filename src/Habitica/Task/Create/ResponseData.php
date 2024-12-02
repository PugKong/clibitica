<?php

declare(strict_types=1);

namespace App\Habitica\Task\Create;

final readonly class ResponseData
{
    public function __construct(public string $id)
    {
    }
}
