<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\Suggestions;

use App\Command\InputMapper\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Strings implements Example
{
    public const array SUGGESTIONS = ['foo', 'bar'];

    public function __construct(
        #[Argument('string', suggestions: self::SUGGESTIONS)]
        public string $string,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'string' => new InputArgument(
                name: 'string',
                mode: InputArgument::REQUIRED,
                description: '',
                default: null,
                suggestedValues: self::SUGGESTIONS,
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'string argument suggestion: array' => [
                new ArrayInput(['string' => 'array']),
                new self('array'),
            ],
        ];
    }
}
