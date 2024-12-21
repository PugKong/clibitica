<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class StringRequired implements Example
{
    /**
     * @param string[] $strings
     */
    public function __construct(
        #[Argument('strings')]
        public array $strings,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'strings' => new InputArgument(
                name: 'strings',
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
            'required string array argument: foo' => [
                new ArrayInput(['strings' => ['foo']]),
                new self(['foo']),
            ],
            'required string array argument: bar' => [
                new ArrayInput(['strings' => ['bar']]),
                new self(['bar']),
            ],
        ];
    }
}
