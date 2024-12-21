<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputOption;

final readonly class Floats implements Example
{
    public const array SUGGESTIONS = [24.24, 42.42];

    public function __construct(
        #[Option('float', suggestions: self::SUGGESTIONS)]
        public float $float,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'float' => new InputOption(
                name: 'float',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
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
