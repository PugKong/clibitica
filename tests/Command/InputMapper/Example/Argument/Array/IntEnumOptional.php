<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class IntEnumOptional implements Example
{
    private const array DEFAULT = [IntEnum::FORTY_TWO];

    /**
     * @param IntEnum[] $enum
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
                default: array_map(fn (IntEnum $enum) => $enum->value, self::DEFAULT),
                suggestedValues: [IntEnum::FORTY_TWO->value, IntEnum::TWENTY_FOUR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional int enum array argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional int enum array argument: 24' => [
                new ArrayInput(['enum' => ['24']]),
                new self([IntEnum::TWENTY_FOUR]),
            ],
        ];
    }
}
