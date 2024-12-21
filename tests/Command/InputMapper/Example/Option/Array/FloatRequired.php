<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class FloatRequired implements Example
{
    /**
     * @param float[] $floats
     */
    public function __construct(
        #[Option('floats')]
        public array $floats,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'floats' => new InputOption(
                name: 'floats',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required float array option: 42.42' => [
                new ArrayInput(['--floats' => ['42.42']]),
                new self([42.42]),
            ],
            'required float array option: 24.24' => [
                new ArrayInput(['--floats' => ['24.24']]),
                new self([24.24]),
            ],
        ];
    }
}
