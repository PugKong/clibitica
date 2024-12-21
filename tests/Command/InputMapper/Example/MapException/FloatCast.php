<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class FloatCast implements Example
{
    public function __construct(
        #[Argument('float')]
        public float $float,
    ) {
    }

    public static function cases(): array
    {
        return [
            'bool to float cast' => [
                new ArrayInput(['float' => true]),
                new MapException(
                    new Argument('float'),
                    new CastException(Type::float(), true),
                ),
            ],
        ];
    }
}
