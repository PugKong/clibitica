<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use DateTimeInterface;

final readonly class Daily extends Task
{
    /**
     * @param string[]        $tags
     * @param ChecklistItem[] $checklist
     * @param int[]           $daysOfMonth
     * @param int[]           $weeksOfMonth
     */
    public function __construct(
        string $id,
        Type $type,
        array $tags,
        string $text,
        string $notes,
        Attribute $attribute,
        Difficulty $difficulty,
        DateTimeInterface $createdAt,
        DateTimeInterface $updatedAt,
        public DateTimeInterface $startDate,
        public Frequency $frequency,
        public bool $isDue,
        public bool $completed,
        public int $streak,
        public array $checklist,
        public int $everyX,
        public Repeat $repeat,
        public array $daysOfMonth,
        public array $weeksOfMonth,
    ) {
        parent::__construct($id, $type, $tags, $text, $notes, $attribute, $difficulty, $createdAt, $updatedAt);
    }
}
