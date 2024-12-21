<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Int;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Option('int')]
        public ?int $int = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'int' => new InputOption(
                name: 'int',
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
            'optional nullable int option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable int option: 42' => [
                new ArrayInput(['--int' => '42']),
                new self(42),
            ],
        ];
    }
}
