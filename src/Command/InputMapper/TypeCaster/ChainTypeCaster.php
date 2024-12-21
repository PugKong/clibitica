<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use Symfony\Component\TypeInfo\Type;

final readonly class ChainTypeCaster implements TypeCaster
{
    /**
     * @param TypeCaster[] $casters
     */
    public function __construct(private array $casters)
    {
    }

    public function supports(Type $type): bool
    {
        foreach ($this->casters as $caster) {
            if ($caster->supports($type)) {
                return true;
            }
        }

        return false;
    }

    public function cast(Type $type, mixed $value): mixed
    {
        foreach ($this->casters as $caster) {
            if ($caster->supports($type)) {
                return $caster->cast($type, $value);
            }
        }

        throw new CastException($type, $value);
    }
}
