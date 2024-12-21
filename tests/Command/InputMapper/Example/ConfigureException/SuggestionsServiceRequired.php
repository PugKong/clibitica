<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\ConfigurationException;

final readonly class SuggestionsServiceRequired implements Example
{
    public function __construct(
        #[Argument('int', suggestions: 'forty two')]
        public int $int = 42,
    ) {
    }

    public static function exception(): ConfigurationException
    {
        return ConfigurationException::suggestionsServiceRequired(self::class, 'int');
    }
}
