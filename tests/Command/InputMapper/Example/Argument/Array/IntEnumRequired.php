<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class IntEnumRequired implements Example
{
    /**
     * @param IntEnum[] $enum
     */
    public function __construct(
        #[Argument('enum')]
        public array $enum,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                description: '',
                default: null,
                suggestedValues: [IntEnum::FORTY_TWO->value, IntEnum::TWENTY_FOUR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required int enum array argument: 42' => [
                new ArrayInput(['enum' => ['42']]),
                new self([IntEnum::FORTY_TWO]),
            ],
            'required int enum array argument: 24' => [
                new ArrayInput(['enum' => ['24']]),
                new self([IntEnum::TWENTY_FOUR]),
            ],
        ];
    }
}
