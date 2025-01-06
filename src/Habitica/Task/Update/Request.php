<?php

declare(strict_types=1);

namespace App\Habitica\Task\Update;

use App\Habitica\Task\Attribute;
use App\Habitica\Task\Difficulty;
use App\Habitica\Task\Frequency;
use App\Habitica\Task\Repeat;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

use function count;

final readonly class Request
{
    /**
     * @param int[] $daysOfMonth
     * @param int[] $weeksOfMonth
     */
    public function __construct(
        #[Ignore]
        public string $id,
        ?string $text = null,
        ?Difficulty $difficulty = null,
        ?float $value = null,
        ?string $notes = null,
        ?string $date = null,
        ?bool $collapseChecklist = null,
        ?Attribute $attribute = null,
        ?Frequency $frequency = null,
        ?Repeat $repeat = null,
        ?int $everyX = null,
        array $daysOfMonth = [],
        array $weeksOfMonth = [],
        ?string $startDate = null,
    ) {
        if (null !== $text) {
            $this->text = $text;
        }

        if (null !== $difficulty) {
            $this->difficulty = $difficulty;
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

        if (count($daysOfMonth) > 0) {
            $this->daysOfMonth = $daysOfMonth;
        }

        if (count($weeksOfMonth) > 0) {
            $this->weeksOfMonth = $weeksOfMonth;
        }

        if (null !== $startDate) {
            $this->startDate = $startDate;
        }
    }

    public ?string $text;
    #[SerializedName('priority')]
    public ?Difficulty $difficulty;
    public float $value;
    public string $notes;
    public string $date;
    public bool $collapseChecklist;
    public Attribute $attribute;
    public Frequency $frequency;
    public Repeat $repeat;
    public int $everyX;
    /** @var int[] */
    public array $daysOfMonth;
    /** @var int[] */
    public array $weeksOfMonth;
    public string $startDate;
}
