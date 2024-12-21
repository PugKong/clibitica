<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\String;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class Required implements Example
{
    public function __construct(
        #[Option('string')]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputOption(
                name: 'string',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required string option: foo' => [
                new ArrayInput(['--string' => 'foo']),
                new self('foo'),
            ],
            'required string option: bar' => [
                new ArrayInput(['--string' => 'bar']),
                new self('bar'),
            ],
        ];
    }
}
