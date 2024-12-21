<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Float;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Argument('float')]
        public ?float $float = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'float' => new InputArgument(
                name: 'float',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: self::DEFAULT,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional nullable float argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable float argument: 42.42' => [
                new ArrayInput(['float' => '42.42']),
                new self(42.42),
            ],
        ];
    }
}
