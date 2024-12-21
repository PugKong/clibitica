<?php

declare(strict_types=1);

namespace App\Command\InputMapper\TypeCaster;

use Symfony\Component\TypeInfo\Type;

interface TypeCaster
{
    public function supports(Type $type): bool;

    public function cast(Type $type, mixed $value): mixed;
}
