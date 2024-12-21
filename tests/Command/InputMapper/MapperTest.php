<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper;

use App\Command\InputMapper\Mapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

class MapperTest extends TestCase
{
    #[DataProvider('mappingProvider')]
    public function testMapping(ArrayInput $input, Example\Example $expected): void
    {
        $suggestions = new FakeSuggestions();
        $mapper = new Mapper(TypeResolver::create(), $suggestions);
        $command = new FakeCommand($mapper, $expected::class);

        $command->run($input, new NullOutput());

        $definition = $command->getDefinition();
        self::assertInput(
            $expected::expectedInput($suggestions),
            [...$definition->getArguments(), ...$definition->getOptions()],
        );
        self::assertObjectsSame($expected, $command->result);
    }

    /**
     * @return array<string, mixed>
     */
    public static function mappingProvider(): iterable
    {
        yield from Example\Argument\String\Required::cases();
        yield from Example\Argument\String\Optional::cases();
        yield from Example\Argument\String\OptionalNullable::cases();

        yield from Example\Argument\Int\Required::cases();
        yield from Example\Argument\Int\Optional::cases();
        yield from Example\Argument\Int\OptionalNullable::cases();

        yield from Example\Argument\Float\Required::cases();
        yield from Example\Argument\Float\Optional::cases();
        yield from Example\Argument\Float\OptionalNullable::cases();

        yield from Example\Argument\StringEnum\Required::cases();
        yield from Example\Argument\StringEnum\Optional::cases();
        yield from Example\Argument\StringEnum\OptionalNullable::cases();

        yield from Example\Argument\IntEnum\Required::cases();
        yield from Example\Argument\IntEnum\Optional::cases();
        yield from Example\Argument\IntEnum\OptionalNullable::cases();

        yield from Example\Argument\Array\StringRequired::cases();
        yield from Example\Argument\Array\StringOptional::cases();
        yield from Example\Argument\Array\IntRequired::cases();
        yield from Example\Argument\Array\IntOptional::cases();
        yield from Example\Argument\Array\FloatRequired::cases();
        yield from Example\Argument\Array\FloatOptional::cases();
        yield from Example\Argument\Array\StringEnumRequired::cases();
        yield from Example\Argument\Array\StringEnumOptional::cases();
        yield from Example\Argument\Array\IntEnumRequired::cases();
        yield from Example\Argument\Array\IntEnumOptional::cases();

        yield from Example\Argument\Suggestions\Strings::cases();
        yield from Example\Argument\Suggestions\Service::cases();
        yield from Example\Argument\Suggestions\Integers::cases();
        yield from Example\Argument\Suggestions\Floats::cases();
        yield from Example\Argument\Suggestions\StringEnums::cases();
        yield from Example\Argument\Suggestions\IntEnums::cases();

        yield from Example\Argument\Description::cases();

        yield from Example\Option\String\Required::cases();
        yield from Example\Option\String\Optional::cases();
        yield from Example\Option\String\OptionalNullable::cases();

        yield from Example\Option\Int\Required::cases();
        yield from Example\Option\Int\Optional::cases();
        yield from Example\Option\Int\OptionalNullable::cases();

        yield from Example\Option\Float\Required::cases();
        yield from Example\Option\Float\Optional::cases();
        yield from Example\Option\Float\OptionalNullable::cases();

        yield from Example\Option\StringEnum\Required::cases();
        yield from Example\Option\StringEnum\Optional::cases();
        yield from Example\Option\StringEnum\OptionalNullable::cases();

        yield from Example\Option\IntEnum\Required::cases();
        yield from Example\Option\IntEnum\Optional::cases();
        yield from Example\Option\IntEnum\OptionalNullable::cases();

        yield from Example\Option\Bool\Required::cases();
        yield from Example\Option\Bool\Optional::cases();
        yield from Example\Option\Bool\OptionalNullable::cases();

        yield from Example\Option\Array\StringRequired::cases();
        yield from Example\Option\Array\StringOptional::cases();
        yield from Example\Option\Array\IntRequired::cases();
        yield from Example\Option\Array\IntOptional::cases();
        yield from Example\Option\Array\FloatRequired::cases();
        yield from Example\Option\Array\FloatOptional::cases();
        yield from Example\Option\Array\StringEnumRequired::cases();
        yield from Example\Option\Array\StringEnumOptional::cases();
        yield from Example\Option\Array\IntEnumRequired::cases();
        yield from Example\Option\Array\IntEnumOptional::cases();

        yield from Example\Option\Suggestions\Service::cases();
        yield from Example\Option\Suggestions\Strings::cases();
        yield from Example\Option\Suggestions\Integers::cases();
        yield from Example\Option\Suggestions\Floats::cases();
        yield from Example\Option\Suggestions\StringEnums::cases();
        yield from Example\Option\Suggestions\IntEnums::cases();

        yield from Example\Option\Description::cases();
        yield from Example\Option\Shortcut::cases();
    }

    /**
     * @template T of InputArgument|InputOption
     *
     * @param T[] $expectedInputs
     * @param T[] $actualInputs
     */
    private static function assertInput(array $expectedInputs, array $actualInputs): void
    {
        self::assertEquals($expectedInputs, $actualInputs);

        foreach (array_keys($expectedInputs) as $key) {
            $expectedInput = $expectedInputs[$key];
            $actualInput = $actualInputs[$key];

            $expectedSuggestions = self::getSuggestions($expectedInput);
            $actualSuggestions = self::getSuggestions($actualInput);

            self::assertEquals(
                $expectedSuggestions,
                $actualSuggestions,
                $expectedInput instanceof InputArgument
                    ? "Failed asserting '{$expectedInput->getName()}' argument suggestions"
                    : "Failed asserting '{$expectedInput->getName()}' option suggestions",
            );
        }
    }

    /**
     * @return Suggestion[]
     */
    private static function getSuggestions(InputArgument|InputOption $input): array
    {
        $completionInput = new CompletionInput();
        $completionSuggestions = new CompletionSuggestions();

        $input->complete($completionInput, $completionSuggestions);

        return $completionSuggestions->getValueSuggestions();
    }

    /**
     * @template T of object
     *
     * @param object&T $expected
     * @param object&T $actual
     */
    private static function assertObjectsSame(object $expected, object $actual): void
    {
        $expected = get_object_vars($expected);
        $actual = get_object_vars($actual);

        self::assertSame($expected, $actual);
    }
}
