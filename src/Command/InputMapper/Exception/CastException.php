<?php

declare(strict_types=1);

namespace App\Command\InputMapper\Exception;

use BackedEnum;
use RuntimeException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ObjectType;

use function is_scalar;
use function sprintf;

final class CastException extends RuntimeException
{
    public string $humanType;
    public string $humanValue;

    public function __construct(
        public readonly Type $rawType,
        public readonly mixed $rawValue,
    ) {
        $this->humanType = (string) $this->rawType;
        if ($this->rawType instanceof ObjectType && is_subclass_of($this->rawType->getClassName(), BackedEnum::class)) {
            $this->humanType = implode(
                ', ',
                array_map(
                    fn (BackedEnum $v) => var_export($v->value, true),
                    $this->rawType->getClassName()::cases(),
                ),
            );
        }

        $this->humanValue = get_debug_type($this->rawValue);
        if (is_scalar($this->rawValue)) {
            $this->humanValue = var_export($this->rawValue, true);
        }

        parent::__construct(sprintf('Unable to cast %s to %s', $this->humanValue, $this->humanType));
    }
}
