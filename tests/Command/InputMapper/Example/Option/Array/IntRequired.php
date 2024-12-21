<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class IntRequired implements Example
{
    /**
     * @param int[] $integers
     */
    public function __construct(
        #[Option('integers')]
        public array $integers,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'integers' => new InputOption(
                name: 'integers',
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
            'required int array option: 42' => [
                new ArrayInput(['--integers' => ['42']]),
                new self([42]),
            ],
            'required int array option: 24' => [
                new ArrayInput(['--integers' => ['24']]),
                new self([24]),
            ],
        ];
    }
}
