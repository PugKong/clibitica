<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper;

use App\Command\InputMapper\Exception\MapException;
use App\Command\InputMapper\Mapper;
use DateTimeInterface;
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
use Throwable;

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
        foreach (self::examples(Example\Example::class) as $example) {
            yield substr($example, strlen(__NAMESPACE__) + 1) => [$example];
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
        foreach (self::examples(Example\Example::class) as $example) {
            yield from $example::cases();
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('configureExceptionProvider')]
    public function testConfigureException(string $class, Throwable $exception): void
    {
        $this->expectException($exception::class);
        $this->expectExceptionMessage($exception->getMessage());

        new FakeCommand(new Mapper(TypeResolver::create()), $class);
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function configureExceptionProvider(): iterable
    {
        foreach (self::examples(Example\ConfigureException\Example::class) as $example) {
            yield substr($example, strlen(__NAMESPACE__) + 1) => [$example, $example::exception()];
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('mapExceptionProvider')]
    public function testMapException(string $class, ArrayInput $input, MapException $exception): void
    {
        $this->expectException($exception::class);
        $this->expectExceptionMessage($exception->getMessage());

        $command = new FakeCommand(new Mapper(TypeResolver::create(), new FakeSuggestions()), $class);

        $command->run($input, new NullOutput());
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function mapExceptionProvider(): iterable
    {
        foreach (self::examples(Example\MapException\Example::class) as $example) {
            foreach ($example::cases() as $case => $params) {
                yield $case => [$example, ...$params];
            }
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $base
     *
     * @return iterable<class-string<T>>
     */
    private static function examples(string $base): iterable
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/Example')->name('*.php');
        foreach ($finder as $file) {
            $class = $file->getRelativePathname();
            $class = substr($class, 0, -strlen('.php'));
            $class = __NAMESPACE__.'\\Example\\'.strtr($class, '/', '\\');

            if (!is_subclass_of($class, $base)) {
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
        $normalize = fn ($item) => $item instanceof DateTimeInterface
            ? $item->format(DateTimeInterface::ATOM)
            : $item;

        $expected = array_map($normalize, get_object_vars($expected));
        $actual = array_map($normalize, get_object_vars($actual));

        self::assertSame($expected, $actual);
    }
}
