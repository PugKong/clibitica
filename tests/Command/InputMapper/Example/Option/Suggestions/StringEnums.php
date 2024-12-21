<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Suggestions;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\InputOption;

final readonly class StringEnums implements Example
{
    public function __construct(
        #[Option('enum', suggestions: [StringEnum::FOO])]
        public StringEnum $enum,
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
                suggestedValues: [StringEnum::FOO->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [];
    }
}
