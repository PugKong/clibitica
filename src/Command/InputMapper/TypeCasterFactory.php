<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use App\Command\InputMapper\TypeCaster\ArrayTypeCaster;
use App\Command\InputMapper\TypeCaster\BackedEnumTypeCaster;
use App\Command\InputMapper\TypeCaster\BoolTypeCaster;
use App\Command\InputMapper\TypeCaster\ChainTypeCaster;
use App\Command\InputMapper\TypeCaster\DateTimeTypeCaster;
use App\Command\InputMapper\TypeCaster\FloatTypeCaster;
use App\Command\InputMapper\TypeCaster\IntTypeCaster;
use App\Command\InputMapper\TypeCaster\NullableTypeCaster;
use App\Command\InputMapper\TypeCaster\StringTypeCaster;
use App\Command\InputMapper\TypeCaster\TypeCaster;

final class TypeCasterFactory
{
    public function create(): TypeCaster
    {
        return new ChainTypeCaster([
            $string = new StringTypeCaster(),
            $int = new IntTypeCaster(),
            $float = new FloatTypeCaster(),
            $bool = new BoolTypeCaster(),
            $dateTime = new DateTimeTypeCaster(),
            $backedEnum = new BackedEnumTypeCaster($string, $int),

            ...array_map(
                fn ($caster) => new NullableTypeCaster($caster),
                [$string, $int, $float, $bool, $dateTime, $backedEnum],
            ),

            ...array_map(
                fn ($caster) => new ArrayTypeCaster($caster),
                [$string, $int, $float, $dateTime, $backedEnum],
            ),
        ]);
    }
}
