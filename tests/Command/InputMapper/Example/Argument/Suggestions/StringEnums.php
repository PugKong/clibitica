<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Suggestions;

use App\Command\InputMapper\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use App\Tests\Command\InputMapper\Example\StringEnum;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class StringEnums implements Example
{
    public function __construct(
        #[Argument('enum', suggestions: [StringEnum::FOO])]
        public StringEnum $enum,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'enum' => new InputArgument(
                name: 'enum',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: [StringEnum::FOO->value],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'string enum argument suggestion: array' => [
                new ArrayInput(['enum' => 'foo']),
                new self(StringEnum::FOO),
            ],
        ];
    }
}
