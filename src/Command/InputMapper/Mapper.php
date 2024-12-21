<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

final readonly class Mapper
{
    private Configure $configure;
    private Map $map;

    public function __construct(TypeResolver $typeResolver, ?Suggestions $suggestions = null)
    {
        $caster = (new TypeCasterFactory())->create();

        $this->configure = new Configure($typeResolver, $caster, $suggestions);
        $this->map = new Map($typeResolver, $caster);
    }

    /**
     * @param class-string $class
     */
    public function configure(Command $command, string $class): void
    {
        $this->configure->configure($command, $class);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function map(InputInterface $input, string $class): mixed
    {
        return $this->map->map($input, $class);
    }
}
