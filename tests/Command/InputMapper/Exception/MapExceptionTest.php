<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Exception;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use App\Tests\Command\InputMapper\Example\StringEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class MapExceptionTest extends TestCase
{
    /**
     * @param callable(): CastException $exception
     */
    #[DataProvider('messageProvider')]
    public function testMessage(callable $exception, string $message): void
    {
        self::assertSame($message, $exception()->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    public static function messageProvider(): array
    {
        return [
            'argument' => [
                fn () => new MapException(
                    new Argument('foo'),
                    new CastException(Type::int(), 'foo'),
                ),
                'Argument foo should be int, but \'foo\' given',
            ],
            'option' => [
                fn () => new MapException(
                    new Option('bar'),
                    new CastException(Type::float(), 'bar'),
                ),
                'Option --bar should be float, but \'bar\' given',
            ],
            'backed enum' => [
                fn () => new MapException(
                    new Argument('enum'),
                    new CastException(Type::enum(StringEnum::class), 'baz'),
                ),
                'Argument enum should be \'foo\', \'bar\', but \'baz\' given',
            ],
        ];
    }

    public function testSetPrevious(): void
    {
        $exception = new MapException(
            new Argument('foo'),
            $previous = new CastException(Type::int(), 'foo'),
        );

        self::assertSame($previous, $exception->getPrevious());
    }
}
