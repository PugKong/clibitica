<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\NullableType;

/**
 * @template T of Type
 */
final readonly class NullableTypeCaster implements TypeCaster
{
    public function __construct(private TypeCaster $caster)
    {
    }

    /**
     * @phpstan-assert-if-true NullableType<T> $type
     */
    public function supports(Type $type): bool
    {
        if (!$type instanceof NullableType) {
            return false;
        }

        return $this->caster->supports($type->getWrappedType());
    }

    /**
     * @param NullableType<T> $type
     */
    public function cast(Type $type, mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        return $this->caster->cast($type->getWrappedType(), $value);
    }
}
