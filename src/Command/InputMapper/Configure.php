<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Exception\ConfigurationException;
use App\Command\InputMapper\TypeCaster\TypeCaster;
use BackedEnum;
use Closure;
use ReflectionParameter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

use function count;
use function is_array;
use function is_string;

final readonly class Configure
{
    public function __construct(
        private TypeResolver $typeResolver,
        private TypeCaster $caster,
        private ?Suggestions $suggestions,
    ) {
    }

    /**
     * @param class-string $class
     */
    public function configure(Command $command, string $class): void
    {
        foreach (Util::constructor($class)->getParameters() as $parameter) {
            $type = $this->typeResolver->resolve($parameter);
            if (!$this->caster->supports($type)) {
                throw ConfigurationException::unsupportedParameterType($class, $parameter->getName(), $type);
            }

            $attribute = Util::attribute($class, $parameter);
            $default = $this->default($parameter);
            $suggestions = $this->suggestions($class, $parameter, $attribute);

            if ($attribute instanceof Argument) {
                $command->addArgument(
                    name: $attribute->name,
                    mode: $this->argumentMode($parameter, $type),
                    description: $attribute->description,
                    default: $default,
                    suggestedValues: $suggestions,
                );
            } else {
                $command->addOption(
                    name: $attribute->name,
                    shortcut: $attribute->shortcut,
                    mode: $this->optionMode($parameter, $type),
                    description: $attribute->description,
                    default: $default ?: null,
                    suggestedValues: $suggestions,
                );
            }
        }
    }

    private function argumentMode(ReflectionParameter $parameter, Type $type): int
    {
        $mode = $parameter->isDefaultValueAvailable() ? InputArgument::OPTIONAL : InputArgument::REQUIRED;

        if ($type->isIdentifiedBy(TypeIdentifier::ARRAY)) {
            $mode |= InputArgument::IS_ARRAY;
        }

        return $mode;
    }

    private function optionMode(ReflectionParameter $parameter, Type $type): int
    {
        $mode = InputOption::VALUE_REQUIRED;

        if ($type->isIdentifiedBy(TypeIdentifier::BOOL)) {
            $mode = InputOption::VALUE_NONE;
            if (!$parameter->isDefaultValueAvailable() || false !== $parameter->getDefaultValue()) {
                $mode |= InputOption::VALUE_NEGATABLE;
            }
        }

        if ($type->isIdentifiedBy(TypeIdentifier::ARRAY)) {
            $mode |= InputOption::VALUE_IS_ARRAY;
        }

        return $mode;
    }

    private function default(ReflectionParameter $parameter): mixed
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }

        $default = $parameter->getDefaultValue();

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
     * @return list<string>|Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     */
    private function suggestions(string $class, ReflectionParameter $parameter, Argument|Option $attribute): array|Closure
    {
        if (is_string($attribute->suggestions)) {
            if (null === $this->suggestions) {
                throw ConfigurationException::suggestionsServiceRequired($class, $parameter->getName());
            }

            return $this->suggestions->suggester($attribute->suggestions);
        }

        $suggestions = $attribute->suggestions;
        if (0 === count($suggestions)) {
            $enumClass = Util::backedEnumClass($this->typeResolver->resolve($parameter));
            if (null !== $enumClass) {
                $suggestions = $enumClass::cases();
            }
        }

        return $this->caster->cast( // @phpstan-ignore return.type
            Type::array(value: Type::string(), key: Type::int()),
            $suggestions,
        );
    }
}
