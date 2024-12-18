<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;

abstract readonly class Task extends Item
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
        public Attribute $attribute,
        #[SerializedName('priority')]
        public Difficulty $difficulty,
        DateTimeInterface $createdAt,
        DateTimeInterface $updatedAt,
    ) {
        parent::__construct($id, $type, $tags, $text, $notes, $createdAt, $updatedAt);
    }
}
