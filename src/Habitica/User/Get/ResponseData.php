<?php

declare(strict_types=1);

namespace App\Habitica\User\Get;

final readonly class ResponseData
{
    public function __construct(public ResponseStats $stats)
    {
    }
}
