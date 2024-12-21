<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Suggestions;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Floats implements Example
{
    public const array SUGGESTIONS = [24.24, 42.42];

    public function __construct(
        #[Argument('float', suggestions: self::SUGGESTIONS)]
        public float $float,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'float' => new InputArgument(
                name: 'float',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: self::SUGGESTIONS,
            ),
        ];
    }

    public static function cases(): array
    {
        return [];
    }
}
