<?php

declare(strict_types=1);

namespace App\WireMock\Api\List;

final readonly class ResponseRequestDefinition
{
    public function __construct(
        public string $method,
        public string $url,
    ) {
    }
}
