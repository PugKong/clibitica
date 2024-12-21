<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\ConfigureException;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\ConfigurationException;
use Symfony\Component\TypeInfo\Type;

final readonly class Map implements Example
{
    /**
     * @param array<string, string> $map
     */
    public function __construct(
        #[Argument('map')]
        public array $map,
    ) {
    }

    public static function exception(): ConfigurationException
    {
        return ConfigurationException::unsupportedParameterType(
            self::class,
            'map',
            Type::array(Type::string(), Type::string()),
        );
    }
}
