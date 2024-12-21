<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Float;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class Optional implements Example
{
    private const float DEFAULT = 42.42;

    public function __construct(
        #[Option('float')]
        public float $float = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'float' => new InputOption(
                name: 'float',
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
            'optional float option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional float option: 24.24' => [
                new ArrayInput(['--float' => '24.24']),
                new self(24.24),
            ],
        ];
    }
}
