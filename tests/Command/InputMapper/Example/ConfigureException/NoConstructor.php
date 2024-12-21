<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Exception\ConfigurationException;

final readonly class NoConstructor implements Example
{
    public static function exception(): ConfigurationException
    {
        return ConfigurationException::constructorRequired(self::class);
    }
}
