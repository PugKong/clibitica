<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use DateTimeInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class DateTimeCast implements Example
{
    public function __construct(
        #[Argument('date-time')]
        public DateTimeInterface $dateTime,
    ) {
    }

    public static function cases(): array
    {
        return [
            'int to DateTimeInterface cast' => [
                new ArrayInput(['date-time' => 42]),
                new MapException(
                    new Argument('date-time'),
                    new CastException(Type::object(DateTimeInterface::class), 42),
                ),
            ],
            'foo to DateTimeInterface cast' => [
                new ArrayInput(['date-time' => 'foo']),
                new MapException(
                    new Argument('date-time'),
                    new CastException(Type::object(DateTimeInterface::class), 'foo'),
                ),
            ],
        ];
    }
}
