<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper\Example\Argument\DateTime;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Suggestions;
use App\Tests\Command\InputMapper\Example\Example;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

final readonly class Optional implements Example
{
    public function __construct(
        #[Argument('interface')]
        public ?DateTimeInterface $interface = null,
        #[Argument('immutable')]
        public ?DateTimeImmutable $immutable = null,
        #[Argument('mutable')]
        public ?DateTime $mutable = null,
    ) {
    }

    public static function expectedInput(Suggestions $suggestions): array
    {
        return [
            'interface' => new InputArgument(
                name: 'interface',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: null,
                suggestedValues: [],
            ),
            'immutable' => new InputArgument(
                name: 'immutable',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: null,
                suggestedValues: [],
            ),
            'mutable' => new InputArgument(
                name: 'mutable',
                mode: InputArgument::OPTIONAL,
                description: '',
                default: null,
                suggestedValues: [],
            ),
        ];
    }

    public static function cases(): array
    {
        return [
            'optional date time argument: null' => [
                new ArrayInput([]),
                new self(
                    null,
                    null,
                    null,
                ),
            ],
            'optional date time argument: 2007-01-02' => [
                new ArrayInput([
                    'interface' => '2007-01-02',
                    'immutable' => '2007-01-02',
                    'mutable' => '2007-01-02',
                ]),
                new self(
                    new DateTimeImmutable('2007-01-02 00:00:00'),
                    new DateTimeImmutable('2007-01-02 00:00:00'),
                    new DateTime('2007-01-02 00:00:00'),
                ),
            ],
            'optional date time argument: 2008-01-02 01:02:03' => [
                new ArrayInput([
                    'interface' => '2007-01-02 01:02:03',
                    'immutable' => '2007-01-02 01:02:03',
                    'mutable' => '2007-01-02 01:02:03',
                ]),
                new self(
                    new DateTimeImmutable('2007-01-02 01:02:03'),
                    new DateTimeImmutable('2007-01-02 01:02:03'),
                    new DateTime('2007-01-02 01:02:03'),
                ),
            ],
        ];
    }
}
