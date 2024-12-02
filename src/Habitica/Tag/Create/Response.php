<?php

declare(strict_types=1);

namespace App\Habitica\Tag\Create;

final readonly class Response
{
    public function __construct(public ResponseData $data)
    {
    }
}
