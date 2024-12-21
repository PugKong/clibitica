<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class FloatOptional implements Example
{
    private const array DEFAULT = [42.42];

    /**
     * @param float[] $floats
     */
    public function __construct(
        #[Argument('floats')]
        public array $floats = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'floats' => new InputArgument(
                name: 'floats',
                mode: InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                description: '',
                default: self::DEFAULT,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional float array argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional float array argument: 24.24' => [
                new ArrayInput(['floats' => ['24.24']]),
                new self([24.24]),
            ],
        ];
    }
}
