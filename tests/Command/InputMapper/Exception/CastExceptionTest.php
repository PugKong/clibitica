<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Exception;

use App\Command\InputMapper\Exception\CastException;
use App\Tests\Command\InputMapper\Example\IntEnum;
use App\Tests\Command\InputMapper\Example\StringEnum;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class CastExceptionTest extends TestCase
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
            'bool to int' => [fn () => new CastException(Type::int(), true), 'Unable to cast true to int'],
            'array to string' => [
                fn () => new CastException(Type::string(), [2, 4, 42]),
                'Unable to cast array to string',
            ],
            'string to DateTime' => [
                fn () => new CastException(Type::object(DateTime::class), 'forty two'),
                'Unable to cast \'forty two\' to DateTime',
            ],
            'backed enum to float' => [
                fn () => new CastException(Type::float(), StringEnum::FOO),
                'Unable to cast App\Tests\Command\InputMapper\Example\StringEnum to float',
            ],
            'float to backed enum' => [
                fn () => new CastException(Type::enum(IntEnum::class), 42.0),
                'Unable to cast 42.0 to 42, 24',
            ],
        ];
    }
}
