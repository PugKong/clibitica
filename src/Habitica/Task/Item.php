<?php

declare(strict_types=1);

namespace App\Habitica\Task;

use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: 'type',
    mapping: [
        'habit' => Habit::class,
        'daily' => Daily::class,
        'todo' => Todo::class,
        'reward' => Reward::class,
    ],
)]
abstract readonly class Item
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public string $id,
        public Type $type,
        public array $tags,
        public string $text,
        public string $notes,
        public DateTimeInterface $createdAt,
        public DateTimeInterface $updatedAt,
    ) {
    }
}
