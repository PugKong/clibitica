<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Exception\ConfigurationException;
use BackedEnum;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BackedEnumType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;

use function count;

final readonly class Util
{
    /**
     * @param class-string $class
     */
    public static function constructor(string $class): ReflectionMethod
    {
        $classReflection = new ReflectionClass($class);
        $constructor = $classReflection->getConstructor();
        if (null === $constructor) {
            throw ConfigurationException::constructorRequired($class);
        }

        return $constructor;
    }

    public static function attribute(string $class, ReflectionParameter $parameter): Argument|Option
    {
        $attributes = [
            ...$parameter->getAttributes(Argument::class),
            ...$parameter->getAttributes(Option::class),
        ];

        if (1 !== count($attributes)) {
            throw ConfigurationException::exactlyOneAttributeRequired($class, $parameter->getName());
        }

        return $attributes[0]->newInstance();
    }

    /**
     * @return class-string<BackedEnum>|null
     */
    public static function backedEnumClass(Type $type): ?string
    {
        if ($type instanceof NullableType) {
            $type = $type->getWrappedType();
        }

        if ($type instanceof BackedEnumType) {
            return $type->getClassName(); // @phpstan-ignore return.type
        }

        if ($type instanceof CollectionType) {
            $type = $type->getCollectionValueType();
        }

        if ($type instanceof ObjectType && is_subclass_of($type->getClassName(), BackedEnum::class)) {
            return $type->getClassName();
        }

        return null;
    }
}
