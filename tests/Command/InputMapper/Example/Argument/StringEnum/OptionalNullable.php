<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\StringEnum;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class OptionalNullable implements Example
{
    private const null DEFAULT = null;

    public function __construct(
        #[Argument('enum')]
        public ?StringEnum $enum = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: self::DEFAULT,
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional nullable backed enum argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional nullable backed enum argument: foo' => [
                new ArrayInput(['enum' => 'foo']),
                new self(StringEnum::FOO),
            ],
        ];
    }
}
