<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function is_int;
use function is_string;

final readonly class IntTypeCaster implements TypeCaster
{
    /**
     * @phpstan-assert-if-true BuiltinType<TypeIdentifier::INT> $type
     */
    public function supports(Type $type): bool
    {
        return $type instanceof BuiltinType && TypeIdentifier::INT === $type->getTypeIdentifier();
    }

    /**
     * @param BuiltinType<TypeIdentifier::INT> $type
     */
    public function cast(Type $type, mixed $value): int
    {
        if (is_string($value) || is_int($value)) {
            return (int) $value;
        }

        throw new CastException($type, $value);
    }
}
