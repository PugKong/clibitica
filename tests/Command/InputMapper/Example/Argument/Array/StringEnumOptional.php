<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class StringEnumOptional implements Example
{
    private const array DEFAULT = [StringEnum::FOO];

    /**
     * @param StringEnum[] $enum
     */
    public function __construct(
        #[Argument('enum')]
        public array $enum = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                description: '',
                default: array_map(fn (StringEnum $enum) => $enum->value, self::DEFAULT),
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional string enum array argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string enum array argument: bar' => [
                new ArrayInput(['enum' => ['bar']]),
                new self([StringEnum::BAR]),
            ],
        ];
    }
}
