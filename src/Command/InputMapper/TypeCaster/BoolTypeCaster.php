<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function is_bool;

final readonly class BoolTypeCaster implements TypeCaster
{
    /**
     * @phpstan-assert-if-true BuiltinType<TypeIdentifier::FLOAT> $type
     */
    public function supports(Type $type): bool
    {
        return $type instanceof BuiltinType && TypeIdentifier::BOOL === $type->getTypeIdentifier();
    }

    /**
     * @param BuiltinType<TypeIdentifier::FLOAT> $type
     */
    public function cast(Type $type, mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        throw new CastException($type, $value);
    }
}
