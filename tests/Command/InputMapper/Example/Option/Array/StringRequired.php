<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class StringRequired implements Example
{
    /**
     * @param string[] $strings
     */
    public function __construct(
        #[Option('strings')]
        public array $strings,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'strings' => new InputOption(
                name: 'strings',
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
            'required string array option: foo' => [
                new ArrayInput(['--strings' => ['foo']]),
                new self(['foo']),
            ],
            'required string array option: bar' => [
                new ArrayInput(['--strings' => ['bar']]),
                new self(['bar']),
            ],
        ];
    }
}
