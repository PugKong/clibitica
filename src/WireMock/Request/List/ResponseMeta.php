<?php

declare(strict_types=1);

namespace App\WireMock\Request\List;

final readonly class ResponseMeta
{
    public function __construct(public int $total)
    {
    }
}
