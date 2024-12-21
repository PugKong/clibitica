<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Exception;

use App\Command\InputMapper\Exception\ConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class ConfigurationExceptionTest extends TestCase
{
    /**
     * @param callable(): ConfigurationException $exception
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
            'constructor required' => [
                fn () => ConfigurationException::constructorRequired('Tests\Constructor'),
                'Tests\Constructor has no constructor',
            ],
            'exactly one attribute required' => [
                fn () => ConfigurationException::exactlyOneAttributeRequired('Tests\Attribute', 'foo'),
                'Tests\Attribute::__constructor($foo): exactly one "App\Command\InputMapper\Attribute\Argument" or'
                .' "App\Command\InputMapper\Attribute\Option" attribute required',
            ],
            'suggestions service required' => [
                fn () => ConfigurationException::suggestionsServiceRequired('Tests\Service', 'bar'),
                'Tests\Service::__constructor($bar) requires suggestions service to be set',
            ],
            'unsupported parameter type' => [
                fn () => ConfigurationException::unsupportedParameterType(
                    'Tests\Parameter',
                    'baz',
                    Type::string(),
                ),
                'Tests\Parameter::__constructor($baz) has unsupported type: string',
            ],
        ];
    }
}
