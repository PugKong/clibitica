<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class StringEnumRequired implements Example
{
    /**
     * @param StringEnum[] $enum
     */
    public function __construct(
        #[Option('enum')]
        public array $enum,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputOption(
                name: 'enum',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                description: '',
                default: null,
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required string enum array option: foo' => [
                new ArrayInput(['--enum' => ['foo']]),
                new self([StringEnum::FOO]),
            ],
            'required string enum array option: bar' => [
                new ArrayInput(['--enum' => ['bar']]),
                new self([StringEnum::BAR]),
            ],
        ];
    }
}
