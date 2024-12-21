<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\Array;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class StringEnumOptional implements Example
{
    private const array DEFAULT = [StringEnum::FOO];

    /**
     * @param StringEnum[] $enum
     */
    public function __construct(
        #[Option('enum')]
        public array $enum = self::DEFAULT,
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
                default: array_map(fn (StringEnum $enum) => $enum->value, self::DEFAULT),
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional string enum array option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string enum array option: bar' => [
                new ArrayInput(['--enum' => ['bar']]),
                new self([StringEnum::BAR]),
            ],
        ];
    }
}
