<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Float;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Required implements Example
{
    public function __construct(
        #[Argument('float')]
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
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required float argument: 42.42' => [
                new ArrayInput(['float' => '42.42']),
                new self(42.42),
            ],
            'required float argument: 24.24' => [
                new ArrayInput(['float' => '24.24']),
                new self(24.24),
            ],
        ];
    }
}
