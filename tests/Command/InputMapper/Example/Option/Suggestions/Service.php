<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputOption;

final readonly class Service implements Example
{
    private const string SUGGESTION = 'string';

    public function __construct(
        #[Option('string', suggestions: self::SUGGESTION)]
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
                suggestedValues: $suggestions->suggester(self::SUGGESTION),
            ),
        ];
    }

    public static function cases(): array
    {
        return [];
    }
}
