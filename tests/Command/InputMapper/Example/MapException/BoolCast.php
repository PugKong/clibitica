<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\MapException;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\TypeInfo\Type;

final readonly class BoolCast implements Example
{
    public function __construct(
        #[Option('bool')]
        public bool $bool,
    ) {
    }

    public static function cases(): array
    {
        return [
            'string to bool cast' => [
                new ArrayInput(['--bool' => 'foo']),
                new MapException(new Option('bool'), new CastException(Type::bool(), 'foo')),
            ],
        ];
    }
}
