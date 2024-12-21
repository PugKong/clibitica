<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ObjectType;

use function is_string;

final readonly class DateTimeTypeCaster implements TypeCaster
{
    /**
     * @phpstan-assert-if-true ObjectType<DateTimeInterface>|ObjectType<DateTimeImmutable>|ObjectType<DateTime> $type
     */
    public function supports(Type $type): bool
    {
        if (!$type instanceof ObjectType) {
            return false;
        }

        return is_a($type->getClassName(), DateTimeInterface::class, true);
    }

    /**
     * @param ObjectType<DateTimeInterface>|ObjectType<DateTimeImmutable>|ObjectType<DateTime> $type
     */
    public function cast(Type $type, mixed $value): mixed
    {
        $class = $type->getClassName();

        if (DateTimeInterface::class === $class) {
            $class = DateTimeImmutable::class;
        }

        if (!is_string($value)) {
            throw new CastException($type, $value);
        }

        try {
            return new $class($value);
        } catch (DateMalformedStringException) {
            throw new CastException($type, $value);
        }
    }
}
