<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class IntRequired implements Example
{
    /**
     * @param int[] $integers
     */
    public function __construct(
        #[Argument('integers')]
        public array $integers,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'integers' => new InputArgument(
                name: 'integers',
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
            'required int array argument: 42' => [
                new ArrayInput(['integers' => ['42']]),
                new self([42]),
            ],
            'required int array argument: 24' => [
                new ArrayInput(['integers' => ['24']]),
                new self([24]),
            ],
        ];
    }
}
