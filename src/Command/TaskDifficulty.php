<?php

declare(strict_types=1);

namespace App\Command;

use App\Habitica\Task\Priority;
use InvalidArgumentException;

enum TaskDifficulty: string
{
    case TRIVIAL = 'trivial';
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case UNKNOWN = 'unknown';

    /**
     * @return TaskDifficulty[]
     */
    public static function known(): array
    {
        return array_filter(self::cases(), fn (self $case) => self::UNKNOWN !== $case);
    }

    public static function fromPriority(int|float $priority): self
    {
        return match ($priority) {
            Priority::TRIVIAL => self::TRIVIAL,
            Priority::EASY => self::EASY,
            Priority::MEDIUM => self::MEDIUM,
            Priority::HARD => self::HARD,
            default => self::UNKNOWN,
        };
    }

    public function toPriority(): int|float
    {
        return match ($this) {
            self::TRIVIAL => Priority::TRIVIAL,
            self::EASY => Priority::EASY,
            self::MEDIUM => Priority::MEDIUM,
            self::HARD => Priority::HARD,
            self::UNKNOWN => throw new InvalidArgumentException('Cannot convert unknown to priority'),
        };
    }
}
