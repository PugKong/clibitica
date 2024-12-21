<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Int;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Argument('int')]
        public ?int $int = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'int' => new InputArgument(
                name: 'int',
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
            'optional nullable int argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable int argument: 42' => [
                new ArrayInput(['int' => '42']),
                new self(42),
            ],
        ];
    }
}
