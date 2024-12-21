<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputOption;

final readonly class Integers implements Example
{
    public const array SUGGESTIONS = [24, 42];

    public function __construct(
        #[Option('int', suggestions: self::SUGGESTIONS)]
        public int $int,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'int' => new InputOption(
                name: 'int',
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
