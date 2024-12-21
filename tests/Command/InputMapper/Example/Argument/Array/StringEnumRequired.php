<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Array;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class StringEnumRequired implements Example
{
    /**
     * @param StringEnum[] $enum
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
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'required string enum array argument: foo' => [
                new ArrayInput(['enum' => ['foo']]),
                new self([StringEnum::FOO]),
            ],
            'required string enum array argument: bar' => [
                new ArrayInput(['enum' => ['bar']]),
                new self([StringEnum::BAR]),
            ],
        ];
    }
}
