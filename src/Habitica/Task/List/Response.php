<?php

declare(strict_types=1);

namespace App\Habitica\Task\List;

use App\Habitica\Task\Item;

final readonly class Response
{
    /**
     * @param Item[] $data
     */
    public function __construct(public array $data)
    {
    }
}
