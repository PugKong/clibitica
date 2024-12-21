<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper;

use App\Command\InputMapper\Suggestions;
use Closure;
use RuntimeException;
use Symfony\Component\Console\Completion\Suggestion;

use function sprintf;

final readonly class FakeSuggestions implements Suggestions
{
    public function suggester(string $name): Closure
    {
        return match ($name) {
            'string' => $this->string(...),
            'number' => $this->number(...),
            default => throw new RuntimeException(sprintf('Unknown suggestion: %s', $name)),
        };
    }

    /**
     * @return list<Suggestion>
     */
    public function string(): array
    {
        return [
            new Suggestion('foo'),
            new Suggestion('bar'),
            new Suggestion('baz'),
        ];
    }

    /**
     * @return list<Suggestion>
     */
    public function number(): array
    {
        return [
            new Suggestion('1'),
            new Suggestion('2'),
            new Suggestion('3'),
        ];
    }
}
