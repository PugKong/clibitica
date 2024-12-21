<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example;

use App\Command\InputMapper\Suggestions;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

interface Example
{
    /**
     * @return array<string, InputArgument|InputOption>
     */
    public static function expectedInput(Suggestions $suggestions): array;

    /**
     * @return array<string, array{0: ArrayInput, 1: object}>
     */
    public static function cases(): array;
}
