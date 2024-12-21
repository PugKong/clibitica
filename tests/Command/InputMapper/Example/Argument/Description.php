<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Description implements Example
{
    public function __construct(
        #[Argument('string', 'some description')]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputArgument(
                name: 'string',
                mode: InputArgument::REQUIRED,
                description: 'some description',
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
