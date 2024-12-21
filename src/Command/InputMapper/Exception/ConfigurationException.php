<?php

declare(strict_types=1);

namespace App\Command\InputMapper\Exception;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use RuntimeException;
use Symfony\Component\TypeInfo\Type;

use function sprintf;

final class ConfigurationException extends RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function constructorRequired(string $class): self
    {
        return new self(sprintf('%s has no constructor', $class));
    }

    public static function exactlyOneAttributeRequired(string $class, string $parameter): self
    {
        return new self(sprintf(
            '%s::__constructor($%s): exactly one "%s" or "%s" attribute required',
            $class,
            $parameter,
            Argument::class,
            Option::class,
        ));
    }

    public static function suggestionsServiceRequired(string $class, string $parameter): self
    {
        return new self(sprintf(
            '%s::__constructor($%s) requires suggestions service to be set',
            $class,
            $parameter,
        ));
    }

    public static function unsupportedParameterType(string $class, string $parameter, Type $type): self
    {
        return new self(sprintf(
            '%s::__constructor($%s) has unsupported type: %s',
            $class,
            $parameter,
            $type,
        ));
    }
}
