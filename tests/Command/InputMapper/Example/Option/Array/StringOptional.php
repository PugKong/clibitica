<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class StringOptional implements Example
{
    private const array DEFAULT = ['foo'];

    /**
     * @param string[] $strings
     */
    public function __construct(
        #[Option('strings')]
        public array $strings = self::DEFAULT,
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
                default: self::DEFAULT,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional string array option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string array option: bar' => [
                new ArrayInput(['--strings' => ['bar']]),
                new self(['bar']),
            ],
        ];
    }
}
