<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\StringEnum;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Optional implements Example
{
    private const StringEnum DEFAULT = StringEnum::FOO;

    public function __construct(
        #[Argument('enum')]
        public StringEnum $enum = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: self::DEFAULT->value,
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional string enum argument: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string enum argument: bar' => [
                new ArrayInput(['enum' => 'bar']),
                new self(StringEnum::BAR),
            ],
        ];
    }
}
