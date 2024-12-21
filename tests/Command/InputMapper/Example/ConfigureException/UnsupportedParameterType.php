<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\ConfigurationException;
use stdClass;
use Symfony\Component\TypeInfo\Type;

final readonly class UnsupportedParameterType implements Example
{
    public function __construct(
        #[Argument('object')]
        public stdClass $object,
    ) {
    }

    public static function exception(): ConfigurationException
    {
        return ConfigurationException::unsupportedParameterType(self::class, 'object', Type::object(stdClass::class));
    }
}
