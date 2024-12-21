<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;

interface Example
{
    /**
     * @return array<string, array{0: ArrayInput, 1: MapException}>
     */
    public static function cases(): array;
}
