<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class IntOptional implements Example
{
    private const array DEFAULT = [42];

    /**
     * @param int[] $integers
     */
    public function __construct(
        #[Argument('integers')]
        public array $integers = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'integers' => new InputArgument(
                name: 'integers',
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
            'optional int array argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional int array argument: 24' => [
                new ArrayInput(['integers' => ['24']]),
                new self([24]),
            ],
        ];
    }
}
