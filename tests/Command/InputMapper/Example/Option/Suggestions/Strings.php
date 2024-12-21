<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputOption;

final readonly class Strings implements Example
{
    public const array SUGGESTIONS = ['foo', 'bar'];

    public function __construct(
        #[Option('string', suggestions: self::SUGGESTIONS)]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputOption(
                name: 'string',
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
