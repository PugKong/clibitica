<?php

declare(strict_types=1);

namespace App\Habitica\Task\Create;

use App\Habitica\Task\Attribute;
use App\Habitica\Task\Frequency;
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
        int|float|null $priority = null,
        ?float $value = null,
        ?string $notes = null,
        ?string $date = null,
        array $checklist = [],
        ?bool $collapseChecklist = null,
        ?Attribute $attribute = null,
        ?Frequency $frequency = null,
        ?RequestRepeat $repeat = null,
        ?int $everyX = null,
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

        if (null !== $attribute) {
            $this->attribute = $attribute;
        }

        if (null !== $frequency) {
            $this->frequency = $frequency;
        }

        if (null !== $repeat) {
            $this->repeat = $repeat;
        }

        if (null !== $everyX) {
            $this->everyX = $everyX;
        }
    }

    /** @var string[] */
    public array $tags;
    public int|float|null $priority;
    public float $value;
    public string $notes;
    public string $date;
    /** @var RequestChecklist[] */
    public array $checklist;
    public bool $collapseChecklist;
    public Attribute $attribute;
    public Frequency $frequency;
    public RequestRepeat $repeat;
    public int $everyX;
}
