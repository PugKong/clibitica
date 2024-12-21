<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\InputOption;

final readonly class IntEnums implements Example
{
    public function __construct(
        #[Option('enum', suggestions: [IntEnum::FORTY_TWO])]
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
                suggestedValues: [IntEnum::FORTY_TWO->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [];
    }
}
