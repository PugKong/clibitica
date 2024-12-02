<?php

declare(strict_types=1);

namespace App\Habitica\Task\List;

use App\Habitica\Task\Type;

final readonly class ResponseData
{
    public function __construct(public string $id, public string $text, public Type $type)
    {
    }
}
