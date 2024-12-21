<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Exception\ConfigurationException;

final readonly class NoAttributes implements Example
{
    public function __construct(
        public int $int = 42,
    ) {
    }

    public static function exception(): ConfigurationException
    {
        return ConfigurationException::exactlyOneAttributeRequired(self::class, 'int');
    }
}
