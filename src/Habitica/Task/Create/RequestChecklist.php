<?php

declare(strict_types=1);

namespace App\Habitica\Task\Create;

final readonly class RequestChecklist
{
    public function __construct(public string $text, public bool $completed = false)
    {
    }
}
