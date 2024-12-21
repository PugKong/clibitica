<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Suggestions;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Service implements Example
{
    private const string SUGGESTION = 'string';

    public function __construct(
        #[Argument('string', suggestions: self::SUGGESTION)]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputArgument(
                name: 'string',
                mode: InputArgument::REQUIRED,
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
