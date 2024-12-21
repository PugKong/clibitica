<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use BackedEnum;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function is_float;
use function is_int;
use function is_string;

final readonly class StringTypeCaster implements TypeCaster
{
    /**
     * @phpstan-assert-if-true BuiltinType<TypeIdentifier::STRING> $type
     */
    public function supports(Type $type): bool
    {
        return $type instanceof BuiltinType && TypeIdentifier::STRING === $type->getTypeIdentifier();
    }

    /**
     * @param BuiltinType<TypeIdentifier::STRING> $type
     */
    public function cast(Type $type, mixed $value): string
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        throw new CastException($type, $value);
    }
}
