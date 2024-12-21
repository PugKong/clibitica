<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\IntEnum;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class Required implements Example
{
    public function __construct(
        #[Option('enum')]
        public IntEnum $enum,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputOption(
                name: 'enum',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: '',
                default: null,
                suggestedValues: [IntEnum::FORTY_TWO->value, IntEnum::TWENTY_FOUR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required int enum option: 42' => [
                new ArrayInput(['--enum' => '42']),
                new self(IntEnum::FORTY_TWO),
            ],
            'required int enum option: 24' => [
                new ArrayInput(['--enum' => '24']),
                new self(IntEnum::TWENTY_FOUR),
            ],
        ];
    }
}
