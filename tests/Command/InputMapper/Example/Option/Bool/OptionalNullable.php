<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Bool;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class OptionalNullable implements Example
{
    public function __construct(
        #[Option('bool')]
        public ?bool $bool = null,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'bool' => new InputOption(
                name: 'bool',
                shortcut: null,
                mode: InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional nullable bool option: null' => [
                new ArrayInput([]),
                new self(),
            ],
            'optional nullable bool option: true' => [
                new ArrayInput(['--bool' => true]),
                new self(true),
            ],
            'optional nullable bool option: false' => [
                new ArrayInput(['--no-bool' => true]),
                new self(false),
            ],
        ];
    }
}
