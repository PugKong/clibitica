<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use BackedEnum;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BackedEnumType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

use function count;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;

final readonly class Mapper
{
    public function __construct(private TypeResolver $typeResolver, private ?Suggestions $suggestions = null)
    {
    }

    /**
     * @param class-string $class
     */
    public function configure(Command $command, string $class): void
    {
        foreach ($this->constructor($class)->getParameters() as $parameter) {
            $isOptional = $parameter->isDefaultValueAvailable();

            $type = $this->typeResolver->resolve($parameter);
            $isArray = $type->isIdentifiedBy(TypeIdentifier::ARRAY);
            $isBool = $type->isIdentifiedBy(TypeIdentifier::BOOL);

            foreach ($parameter->getAttributes() as $attribute) {
                $attribute = $attribute->newInstance();

                if ($attribute instanceof Argument) {
                    $mode = $isOptional ? InputArgument::OPTIONAL : InputArgument::REQUIRED;
                    if ($isArray) {
                        $mode |= InputArgument::IS_ARRAY;
                    }

                    $command->addArgument(
                        name: $attribute->name,
                        mode: $mode,
                        description: $attribute->description,
                        default: $this->default($parameter),
                        suggestedValues: $this->suggestions($parameter, $attribute),
                    );

                    break;
                }

                if ($attribute instanceof Option) {
                    $mode = InputOption::VALUE_REQUIRED;
                    if ($isBool) {
                        $mode = InputOption::VALUE_NONE;
                        if (!$isOptional || false !== $parameter->getDefaultValue()) {
                            $mode |= InputOption::VALUE_NEGATABLE;
                        }
                    }
                    if ($isArray) {
                        $mode |= InputOption::VALUE_IS_ARRAY;
                    }

                    $command->addOption(
                        name: $attribute->name,
                        shortcut: $attribute->shortcut,
                        mode: $mode,
                        description: $attribute->description,
                        default: $this->default($parameter),
                        suggestedValues: $this->suggestions($parameter, $attribute),
                    );

                    break;
                }
            }
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function map(InputInterface $input, string $class): mixed
    {
        $args = [];
        foreach ($this->constructor($class)->getParameters() as $parameter) {
            $type = $this->typeResolver->resolve($parameter);

            foreach ($parameter->getAttributes() as $attribute) {
                $attribute = $attribute->newInstance();

                if ($attribute instanceof Argument) {
                    $arg = $input->getArgument($attribute->name);

                    $args[] = $this->cast($type, $arg);

                    break;
                }

                if ($attribute instanceof Option) {
                    $arg = $input->getOption($attribute->name);

                    $args[] = $this->cast($type, $arg);

                    break;
                }
            }
        }

        return new $class(...$args);
    }

    /**
     * @param class-string $class
     */
    private function constructor(string $class): ReflectionMethod
    {
        $classReflection = new ReflectionClass($class);
        $constructor = $classReflection->getConstructor();
        if (null === $constructor) {
            throw new RuntimeException(sprintf('Class %s has no constructor', $class));
        }

        return $constructor;
    }

    private function default(ReflectionParameter $parameter): mixed
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }

        $default = $parameter->getDefaultValue();

        if (false === $default) {
            $default = null;
        }

        if ($default instanceof BackedEnum) {
            $default = $default->value;
        }

        if (is_array($default)) {
            foreach ($default as $i => $value) {
                if ($value instanceof BackedEnum) {
                    $default[$i] = $value->value;
                }
            }
        }

        return $default;
    }

    /**
     * @return scalar[]|Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     */
    private function suggestions(ReflectionParameter $parameter, Argument|Option $attribute): array|Closure
    {
        if (is_string($attribute->suggestions)) {
            if (null === $this->suggestions) {
                throw new RuntimeException('Suggestions service was not set');
            }

            return $this->suggestions->suggester($attribute->suggestions);
        }

        $suggestions = $attribute->suggestions;
        if (0 === count($suggestions)) {
            $enumClass = $this->backedEnumClass($this->typeResolver->resolve($parameter));
            if (null !== $enumClass) {
                $suggestions = $enumClass::cases();
            }
        }

        $result = [];
        foreach ($suggestions as $suggestion) {
            if ($suggestion instanceof BackedEnum) {
                $result[] = $suggestion->value;
            } else {
                $result[] = $suggestion;
            }
        }

        return $result;
    }

    /**
     * @return class-string<BackedEnum>|null
     */
    private function backedEnumClass(Type $type): ?string
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

    private function cast(Type $type, mixed $value): mixed
    {
        if ($type->isNullable() && null === $value) {
            return null;
        }

        if ($type instanceof CollectionType) {
            $keyType = $type->getCollectionKeyType();
            $valueType = $type->getCollectionValueType();

            if (!$keyType->isIdentifiedBy(TypeIdentifier::INT)) {
                throw new RuntimeException('Unsupported property collection type: '.$type);
            }

            if (!is_array($value)) {
                throw new RuntimeException('Expected array');
            }

            return array_map(fn ($v) => $this->cast($valueType, $v), $value);
        }

        return match (true) {
            $type->isIdentifiedBy(TypeIdentifier::STRING) => $this->castToString($value),
            $type->isIdentifiedBy(TypeIdentifier::FLOAT) => $this->castToFloat($value),
            $type->isIdentifiedBy(TypeIdentifier::INT) => $this->castToInt($value),
            $type->isIdentifiedBy(TypeIdentifier::BOOL) => $this->castToBool($value),
            ($class = $this->backedEnumClass($type)) !== null => $this->castToBackedEnum($class, $value),
            default => throw new RuntimeException('Unsupported property type: '.$type),
        };
    }

    private function castToString(mixed $value): string
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        throw new RuntimeException(sprintf('Unsupported cast from %s to string', get_debug_type($value)));
    }

    private function castToFloat(mixed $value): float
    {
        if (is_string($value) || is_float($value) || is_int($value)) {
            return (float) $value;
        }

        throw new RuntimeException(sprintf('Unsupported cast from %s to int', get_debug_type($value)));
    }

    private function castToInt(mixed $value): int
    {
        if (is_string($value) || is_int($value)) {
            return (int) $value;
        }

        throw new RuntimeException(sprintf('Unsupported cast from %s to int', get_debug_type($value)));
    }

    private function castToBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        throw new RuntimeException(sprintf('Unsupported cast from %s to bool', get_debug_type($value)));
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $class
     *
     * @return BackedEnum of T
     */
    private function castToBackedEnum(string $class, mixed $value): BackedEnum
    {
        /** @var BackedEnumType<T, BuiltinType<TypeIdentifier::INT>|BuiltinType<TypeIdentifier::STRING>> $type */
        $type = Type::enum($class);
        $type = $type->getBackingType()->getTypeIdentifier();

        /** @noinspection PhpUncoveredEnumCasesInspection */
        $value = match ($type) {
            TypeIdentifier::STRING => $this->castToString($value),
            TypeIdentifier::INT => $this->castToInt($value),
        };

        return $class::from($value);
    }
}
