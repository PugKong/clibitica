<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class IntCast implements Example
{
    public function __construct(
        #[Argument('int')]
        public int $int,
    ) {
    }

    public static function cases(): array
    {
        return [
            'bool to int cast' => [
                new ArrayInput(['int' => true]),
                new MapException(
                    new Argument('int'),
                    new CastException(Type::int(), true),
                ),
            ],
        ];
    }
}
