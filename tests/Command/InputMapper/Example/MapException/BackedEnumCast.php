<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use App\Tests\Command\InputMapper\Example\IntEnum;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class BackedEnumCast implements Example
{
    public function __construct(
        #[Option('string')]
        public ?StringEnum $string,
        #[Option('int')]
        public ?IntEnum $int,
    ) {
    }

    public static function cases(): array
    {
        return [
            'unknown string enum value' => [
                new ArrayInput(['--string' => 'baz']),
                new MapException(
                    new Option('string'),
                    new CastException(Type::enum(StringEnum::class), 'baz'),
                ),
            ],
            'unknown int enum value' => [
                new ArrayInput(['--int' => 4242]),
                new MapException(
                    new Option('int'),
                    new CastException(Type::enum(IntEnum::class), 4242),
                ),
            ],
        ];
    }
}
