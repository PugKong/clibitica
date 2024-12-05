<?php

declare(strict_types=1);

namespace App\WireMock\Request\List;

final readonly class ResponseRequest
{
    public function __construct(
        public string $id,
        public ResponseRequestDefinition $request,
    ) {
    }
}
