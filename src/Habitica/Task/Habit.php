<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use DateTimeInterface;

final readonly class Habit extends Task
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        string $id,
        Type $type,
        array $tags,
        string $text,
        string $notes,
        Attribute $attribute,
        Difficulty $difficulty,
        public Frequency $frequency,
        public int $counterUp,
        public int $counterDown,
        DateTimeInterface $createdAt,
        DateTimeInterface $updatedAt,
    ) {
        parent::__construct($id, $type, $tags, $text, $notes, $attribute, $difficulty, $createdAt, $updatedAt);
    }
}
