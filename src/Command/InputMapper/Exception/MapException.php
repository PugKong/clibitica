<?php

declare(strict_types=1);

namespace App\Command\InputMapper\Exception;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use RuntimeException;

use function sprintf;

final class MapException extends RuntimeException
{
    public function __construct(Argument|Option $attribute, CastException $cast)
    {
        $message = match (true) {
            $attribute instanceof Argument => sprintf(
                'Argument %s should be %s, but %s given',
                $attribute->name,
                $cast->humanType,
                $cast->humanValue,
            ),
            $attribute instanceof Option => sprintf(
                'Option --%s should be %s, but %s given',
                $attribute->name,
                $cast->humanType,
                $cast->humanValue,
            ),
        };

        parent::__construct($message, previous: $cast);
    }
}
