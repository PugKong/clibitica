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
use Symfony\Component\Finder\Finder;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

use function strlen;

class MapperTest extends TestCase
{
    /**
     * @param class-string<Example\Example> $example
     */
    #[DataProvider('configureProvider')]
    public function testConfigure(string $example): void
    {
        $suggestions = new FakeSuggestions();

        $command = new FakeCommand(new Mapper(TypeResolver::create(), $suggestions), $example);
        $definition = $command->getDefinition();

        self::assertInput(
            $example::expectedInput($suggestions),
            [...$definition->getArguments(), ...$definition->getOptions()],
        );
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function configureProvider(): iterable
    {
        foreach (self::examples() as $example) {
            yield $example => [$example];
        }
    }

    #[DataProvider('mapProvider')]
    public function testMap(ArrayInput $input, Example\Example $expected): void
    {
        $command = new FakeCommand(new Mapper(TypeResolver::create(), new FakeSuggestions()), $expected::class);

        $command->run($input, new NullOutput());

        self::assertObjectsSame($expected, $command->result);
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function mapProvider(): iterable
    {
        foreach (self::examples() as $example) {
            yield from $example::cases();
        }
    }

    /**
     * @return iterable<class-string<Example\Example>>
     */
    private static function examples(): iterable
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/Example')->name('*.php');
        foreach ($finder as $file) {
            $class = $file->getRelativePathname();
            $class = substr($class, 0, -strlen('.php'));
            $class = __NAMESPACE__.'\\Example\\'.strtr($class, '/', '\\');

            if (!is_subclass_of($class, Example\Example::class)) {
                continue;
            }

            yield $class;
        }
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
