<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Option\StringEnum;

use App\Command\InputMapper\Attribute\Option;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

final readonly class Optional implements Example
{
    private const StringEnum DEFAULT = StringEnum::FOO;

    public function __construct(
        #[Option('enum')]
        public StringEnum $enum = self::DEFAULT,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputOption(
                name: 'enum',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: '',
                default: self::DEFAULT->value,
                suggestedValues: [StringEnum::FOO->value, StringEnum::BAR->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional string enum option: default' => [
                new ArrayInput([]),
                new self(self::DEFAULT),
            ],
            'optional string enum option: bar' => [
                new ArrayInput(['--enum' => 'bar']),
                new self(StringEnum::BAR),
            ],
        ];
    }
}
