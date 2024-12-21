<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function is_array;

final readonly class ArrayTypeCaster implements TypeCaster
{
    public function __construct(private TypeCaster $caster)
    {
    }

    /**
     * @phpstan-assert-if-true CollectionType<BuiltinType<TypeIdentifier::ARRAY>> $type
     */
    public function supports(Type $type): bool
    {
        if (!$type instanceof CollectionType) {
            return false;
        }

        $keyType = $type->getCollectionKeyType();
        if (!$keyType instanceof BuiltinType || TypeIdentifier::INT !== $keyType->getTypeIdentifier()) {
            return false;
        }

        return $this->caster->supports($type->getCollectionValueType());
    }

    /**
     * @param CollectionType<BuiltinType<TypeIdentifier::ARRAY>> $type
     */
    public function cast(Type $type, mixed $value): mixed
    {
        if (!is_array($value) || !array_is_list($value)) {
            throw new CastException($type, $value);
        }

        return array_map(fn ($value) => $this->caster->cast($type->getCollectionValueType(), $value), $value);
    }
}
