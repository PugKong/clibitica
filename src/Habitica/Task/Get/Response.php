<?php

declare(strict_types=1);

namespace App\Habitica\Task\Get;

use App\Habitica\Task\Item;

final readonly class Response
{
    public function __construct(public Item $data)
    {
    }
}