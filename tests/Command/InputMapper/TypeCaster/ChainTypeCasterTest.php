<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\TypeCaster;

use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\TypeCaster\ChainTypeCaster;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class ChainTypeCasterTest extends TestCase
{
    public function testCastUnsupportedType(): void
    {
        $expected = new CastException($type = Type::mixed(), $value = 42);

        $this->expectException($expected::class);
        $this->expectExceptionMessage($expected->getMessage());

        $caster = new ChainTypeCaster([]);

        $caster->cast($type, $value);
    }
}
