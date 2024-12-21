<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Int;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Required implements Example
{
    public function __construct(
        #[Argument('int')]
        public int $int,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'int' => new InputArgument(
                name: 'int',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required int argument: 42' => [
                new ArrayInput(['int' => '42']),
                new self(42),
            ],
            'required int argument: 24' => [
                new ArrayInput(['int' => '24']),
                new self(24),
            ],
        ];
    }
}
