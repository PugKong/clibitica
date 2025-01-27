<?php

declare(strict_types=1);

namespace App\WireMock\Api\List;

final readonly class Response
{
    public function __construct(
        public ResponseMeta $meta,
        /** @var ResponseRequest[] */
        public array $requests,
    ) {
    }
}
