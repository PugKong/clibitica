<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ObjectType;
use ValueError;

final readonly class BackedEnumTypeCaster implements TypeCaster
{
    public function __construct(private StringTypeCaster $stringTypeCaster, private IntTypeCaster $intTypeCaster)
    {
    }

    /**
     * @phpstan-assert-if-true ObjectType<BackedEnum> $type
     */
    public function supports(Type $type): bool
    {
        if ($type instanceof ObjectType) {
            return is_subclass_of($type->getClassName(), BackedEnum::class);
        }

        return false;
    }

    /**
     * @param ObjectType<BackedEnum> $type
     *
     * @throws ReflectionException
     */
    public function cast(Type $type, mixed $value): BackedEnum
    {
        /** @var class-string<BackedEnum> $class */
        $class = $type->getClassName();

        $reflectionBackingType = (new ReflectionEnum($class))->getBackingType();
        $caster = 'string' === (string) $reflectionBackingType ? $this->stringTypeCaster : $this->intTypeCaster;

        try {
            return $class::from($caster->cast(Type::int(), $value));
        } catch (ValueError) {
            throw new CastException($type, $value);
        }
    }
}
