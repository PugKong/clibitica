<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\IntEnum;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Required implements Example
{
    public function __construct(
        #[Argument('enum')]
        public IntEnum $enum,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: [IntEnum::FORTY_TWO->value, IntEnum::TWENTY_FOUR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required int enum argument: 42' => [
                new ArrayInput(['enum' => '42']),
                new self(IntEnum::FORTY_TWO),
            ],
            'required int enum argument: 24' => [
                new ArrayInput(['enum' => '24']),
                new self(IntEnum::TWENTY_FOUR),
            ],
        ];
    }
}
