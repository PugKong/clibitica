<?php

declare(strict_types=1);

namespace App\WireMock\Api\List;

final readonly class ResponseMeta
{
    public function __construct(public int $total)
    {
    }
}
