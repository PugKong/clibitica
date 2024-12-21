<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputOption;

final readonly class Shortcut implements Example
{
    public function __construct(
        #[Option('string', shortcut: 's')]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputOption(
                name: 'string',
                shortcut: 's',
                mode: InputOption::VALUE_REQUIRED,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [];
    }
}
