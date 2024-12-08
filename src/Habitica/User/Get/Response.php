<?php

declare(strict_types=1);

namespace App\Habitica\User\Get;

final readonly class Response
{
    public function __construct(public ResponseData $data)
    {
    }
}
