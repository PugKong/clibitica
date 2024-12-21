<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class StringCast implements Example
{
    public function __construct(
        #[Argument('string')]
        public string $string,
    ) {
    }

    public static function cases(): array
    {
        return [
            'bool to string cast' => [
                new ArrayInput(['string' => true]),
                new MapException(
                    new Argument('string'),
                    new CastException(Type::string(), true),
                ),
            ],
        ];
    }
}
