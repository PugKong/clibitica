<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Attribute\Option;
use App\Command\Suggestions;
use App\Habitica\Task\Attribute;
use App\Habitica\Task\Difficulty;
use App\Habitica\Task\Frequency;

final readonly class UpdateInput
{
    /**
     * @param Repeat[] $repeat
     * @param int[]    $daysOfMonth
     * @param int[]    $weeksOfMonth
     */
    public function __construct(
        #[Argument('id', 'The task id or alias', Suggestions::TASK_ID)]
        public string $task,
        #[Option('text', 'The text to be displayed for the task')]
        public ?string $text = null,
        #[Option('difficulty', 'Task difficulty, options are: "trivial", "easy", "medium", "hard"')]
        public ?Difficulty $difficulty = null,
        #[Option('cost', 'Only valid for type "reward." The cost in gold of the reward')]
        public ?float $cost = null,
        #[Option('notes', 'Extra notes')]
        public ?string $notes = null,
        #[Option('date', 'Due date in Y-m-d format to be shown in task list. Only valid for type "todo"')]
        public ?string $date = null,
        #[Option('checklist-collapse', 'Determines if a checklist will be displayed')]
        public ?bool $checklistCollapse = null,
        #[Option('attribute', 'User\'s attribute to use, options are: "str", "int", "per", "con"')]
        public ?Attribute $attribute = null,
        #[Option(
            'frequency',
            'Values "weekly" and "monthly" enable use of the "--repeat" flag.'
            .'All frequency values enable use of the "--every" flag.'
            .'Value "monthly" enables use of the "--weeks-of-month" and "--days-of-month" flags.'
            .'Frequency is only valid for type "daily"',
        )]
        public ?Frequency $frequency = null,
        #[Option(
            'repeat',
            'Only valid for type "daily". Value of frequency must be "weekly". Days are: su, mo, tu, we, th, fr, sa',
        )]
        public array $repeat = [],
        #[Option(
            'every',
            'Only valid for type "daily", the number of days/weeks/months/years (depends on frequency)'
            .' until this task is available again',
        )]
        public ?int $everyX = null,
        #[Option('days-of-month', 'Days of month. Only valid for "monthly" frequency')]
        public array $daysOfMonth = [],
        #[Option('weeks-of-month', 'Weeks of month. Only valid for "monthly" frequency')]
        public array $weeksOfMonth = [],
        #[Option(
            'start-date',
            'Date in Y-m-d format when the task will first become available. Only valid for type "daily"',
        )]
        public ?string $startDate = null,
    ) {
    }
}
