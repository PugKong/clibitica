<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\Console\Command\Command;

final readonly class CommandResult
{
    public function __construct(public int $code = Command::SUCCESS, public string $output = '')
    {
    }
}
