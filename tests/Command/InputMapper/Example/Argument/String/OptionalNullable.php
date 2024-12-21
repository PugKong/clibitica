<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\String;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Argument('string')]
        public ?string $string = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputArgument(
                name: 'string',
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
            'optional nullable string argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable string argument: bar' => [
                new ArrayInput(['string' => 'bar']),
                new self('bar'),
            ],
        ];
    }
}
