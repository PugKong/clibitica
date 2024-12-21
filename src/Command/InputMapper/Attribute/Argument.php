<?php

declare(strict_types=1);

namespace App\Command\InputMapper\Attribute;

use Attribute;
use BackedEnum;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Argument
{
    /**
     * @param string|scalar[]|BackedEnum[] $suggestions
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public string|array $suggestions = [],
    ) {
    }
}
