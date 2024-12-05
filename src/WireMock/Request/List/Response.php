<?php

declare(strict_types=1);

namespace App\WireMock\Request\List;

final readonly class Response
{
    public function __construct(
        public ResponseMeta $meta,
        /** @var ResponseRequest[] */
        public array $requests,
    ) {
    }
}
