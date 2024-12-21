<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Suggestions;

use App\Command\InputMapper\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\IntEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class IntEnums implements Example
{
    public function __construct(
        #[Argument('enum', suggestions: [IntEnum::FORTY_TWO])]
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
                suggestedValues: [IntEnum::FORTY_TWO->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'int enum argument suggestion: array' => [
                new ArrayInput(['enum' => '42']),
                new self(IntEnum::FORTY_TWO),
            ],
        ];
    }
}
