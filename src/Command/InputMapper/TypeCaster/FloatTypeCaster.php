<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function is_float;
use function is_int;
use function is_string;

final readonly class FloatTypeCaster implements TypeCaster
{
    /**
     * @phpstan-assert-if-true BuiltinType<TypeIdentifier::FLOAT> $type
     */
    public function supports(Type $type): bool
    {
        return $type instanceof BuiltinType && TypeIdentifier::FLOAT === $type->getTypeIdentifier();
    }

    /**
     * @param BuiltinType<TypeIdentifier::FLOAT> $type
     */
    public function cast(Type $type, mixed $value): float
    {
        if (is_string($value) || is_float($value) || is_int($value)) {
            return (float) $value;
        }

        throw new CastException($type, $value);
    }
}
