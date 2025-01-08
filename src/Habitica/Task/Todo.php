<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use DateTimeInterface;

final readonly class Todo extends Task
{
    /**
     * @param string[]        $tags
     * @param ChecklistItem[] $checklist
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
        public ?DateTimeInterface $date,
        public array $checklist,
        public bool $collapseChecklist,
    ) {
        parent::__construct($id, $type, $tags, $text, $notes, $attribute, $difficulty, $createdAt, $updatedAt);
    }
}
