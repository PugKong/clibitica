<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\String;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Required implements Example
{
    public function __construct(
        #[Argument('string')]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputArgument(
                name: 'string',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required string argument: foo' => [
                new ArrayInput(['string' => 'foo']),
                new self('foo'),
            ],
            'required string argument: bar' => [
                new ArrayInput(['string' => 'bar']),
                new self('bar'),
            ],
        ];
    }
}
