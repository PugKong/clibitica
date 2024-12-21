<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\String;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Option('string')]
        public ?string $string = self::DEFAULT,
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
                default: self::DEFAULT,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional nullable string option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable string option: bar' => [
                new ArrayInput(['--string' => 'bar']),
                new self('bar'),
            ],
        ];
    }
}
