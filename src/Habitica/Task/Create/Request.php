<?php

declare(strict_types=1);

namespace App\Habitica\Task\Create;

use App\Habitica\Task\Priority;
use App\Habitica\Task\Type;

use function count;

final readonly class Request
{
    /**
     * @param string[]           $tags
     * @param RequestChecklist[] $checklist
     */
    public function __construct(
        public Type $type,
        public string $text,
        array $tags = [],
        ?Priority $priority = null,
        ?float $value = null,
        ?string $notes = null,
        ?string $date = null,
        array $checklist = [],
        ?bool $collapseChecklist = null,
    ) {
        if (count($tags) > 0) {
            $this->tags = $tags;
        }

        if (null !== $priority) {
            $this->priority = $priority;
        }

        if (null !== $value) {
            $this->value = $value;
        }

        if (null !== $notes) {
            $this->notes = $notes;
        }

        if (null !== $date) {
            $this->date = $date;
        }

        if (count($checklist) > 0) {
            $this->checklist = $checklist;
        }

        if (null !== $collapseChecklist) {
            $this->collapseChecklist = $collapseChecklist;
        }
    }

    /** @var string[] */
    public array $tags;
    public Priority $priority;
    public float $value;
    public string $notes;
    public string $date;
    /** @var RequestChecklist[] */
    public array $checklist;
    public bool $collapseChecklist;
}
