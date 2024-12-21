<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use App\Command\InputMapper\TypeCaster\TypeCaster;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

final readonly class Map
{
    public function __construct(private TypeResolver $typeResolver, private TypeCaster $caster)
    {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws MapException
     */
    public function map(InputInterface $input, string $class): mixed
    {
        $args = [];
        foreach (Util::constructor($class)->getParameters() as $parameter) {
            $attribute = Util::attribute($class, $parameter);

            $value = $attribute instanceof Argument
                ? $input->getArgument($attribute->name)
                : $input->getOption($attribute->name);

            try {
                $args[] = $this->caster->cast($this->typeResolver->resolve($parameter), $value);
            } catch (CastException $exception) {
                throw new MapException($attribute, $exception);
            }
        }

        return new $class(...$args);
    }
}
