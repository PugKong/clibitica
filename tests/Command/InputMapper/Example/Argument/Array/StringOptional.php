<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class StringOptional implements Example
{
    private const array DEFAULT = ['foo'];

    /**
     * @param string[] $strings
     */
    public function __construct(
        #[Argument('strings')]
        public array $strings = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'strings' => new InputArgument(
                name: 'strings',
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
            'optional string array argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string array argument: bar' => [
                new ArrayInput(['strings' => ['bar']]),
                new self(['bar']),
            ],
        ];
    }
}
