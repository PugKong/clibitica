<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class ArrayCast implements Example
{
    /**
     * @param string[] $array
     */
    public function __construct(
        #[Argument('array')]
        public array $array,
    ) {
    }

    public static function cases(): array
    {
        return [
            'string to array cast' => [
                new ArrayInput(['array' => 'foo']),
                new MapException(
                    new Argument('array'),
                    new CastException(Type::array(Type::string(), Type::int()), 'foo'),
                ),
            ],
        ];
    }
}
