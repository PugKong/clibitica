<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class FloatRequired implements Example
{
    /**
     * @param float[] $floats
     */
    public function __construct(
        #[Argument('floats')]
        public array $floats,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'floats' => new InputArgument(
                name: 'floats',
                mode: InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required float array argument: 42.42' => [
                new ArrayInput(['floats' => ['42.42']]),
                new self([42.42]),
            ],
            'required float array argument: 24.24' => [
                new ArrayInput(['floats' => ['24.24']]),
                new self([24.24]),
            ],
        ];
    }
}
