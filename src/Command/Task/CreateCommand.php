<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Task\Attribute;
use App\Habitica\Task\Create\Request;
use App\Habitica\Task\Create\RequestChecklist;
use App\Habitica\Task\Difficulty;
use App\Habitica\Task\Frequency;
use App\Habitica\Task\Repeat;
use App\Habitica\Task\Type;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

use function count;
use function in_array;

#[AsCommand(name: 'task:create', description: 'Create a new task')]
final class CreateCommand extends Command
{
    private const array REPEAT = ['su', 'mo', 'tu', 'we', 'th', 'fr', 'sa'];

    public function __construct(private readonly Habitica $habitica, private readonly Suggestions $suggestions)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            name: 'text',
            mode: InputArgument::REQUIRED,
            description: 'The text to be displayed for the task',
        );

        $this->addOption(
            name: 'type',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Task type, options are: "habit", "daily", "todo", "reward"',
            default: 'todo',
            suggestedValues: $this->suggestions->taskType(...),
        );

        $this->addOption(
            name: 'tags',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'UUID of tag',
            suggestedValues: $this->suggestions->tagId(...),
        );

        $this->addOption(
            name: 'difficulty',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Task difficulty, options are: "trivial", "easy", "medium", "hard"',
            suggestedValues: $this->suggestions->taskDifficulty(...),
        );

        $this->addOption(
            name: 'cost',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Only valid for type "reward." The cost in gold of the reward',
        );

        $this->addOption(
            name: 'notes',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Extra notes',
        );

        $this->addOption(
            name: 'date',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Due date in Y-m-d format to be shown in task list. Only valid for type "todo"',
        );

        $this->addOption(
            name: 'checklist',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Checklist items',
        );

        $this->addOption(
            name: 'checklist-collapse',
            mode: InputOption::VALUE_NEGATABLE,
            description: 'Determines if a checklist will be displayed',
        );

        $this->addOption(
            name: 'attribute',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'User\'s attribute to use, options are: "str", "int", "per", "con"',
            suggestedValues: $this->suggestions->attribute(...),
        );

        $this->addOption(
            name: 'frequency',
            mode: InputOption::VALUE_OPTIONAL,
            description: implode(' ', [
                'Values "weekly" and "monthly" enable use of the "--repeat" flag.',
                'All frequency values enable use of the "--every" flag.',
                'Value "monthly" enables use of the "--weeks-of-month" and "days-of-month" flags.',
                'Frequency is only valid for type "daily"',
            ]),
            suggestedValues: $this->suggestions->frequency(...),
        );

        $this->addOption(
            name: 'repeat',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Only valid for type "daily". Value of frequency must be "weekly". Days are: '.implode(', ', self::REPEAT),
            suggestedValues: self::REPEAT,
        );

        $this->addOption(
            name: 'every',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Only valid for type "daily", the number of days/weeks/months/years (depends on frequency) until this task is available again',
        );

        $this->addOption(
            name: 'days-of-month',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Days of month. Only valid for "monthly" frequency',
        );

        $this->addOption(
            name: 'weeks-of-month',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Weeks of month. Only valid for "monthly" frequency',
        );

        $this->addOption(
            name: 'start-date',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Date in Y-m-d format when the task will first become available. Only valid for type "daily"',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::string($type = $input->getOption('type'));
        Assert::string($text = $input->getArgument('text'));
        Assert::isArray($tags = $input->getOption('tags'));
        Assert::allString($tags);
        Assert::nullOrString($difficulty = $input->getOption('difficulty'));
        Assert::nullOrString($cost = $input->getOption('cost'));
        Assert::nullOrString($notes = $input->getOption('notes'));
        Assert::nullOrString($date = $input->getOption('date'));
        Assert::isArray($checklist = $input->getOption('checklist'));
        Assert::allString($checklist);
        Assert::nullOrBoolean($checklistCollapse = $input->getOption('checklist-collapse'));
        Assert::nullOrString($attribute = $input->getOption('attribute'));
        Assert::nullOrString($frequency = $input->getOption('frequency'));
        Assert::nullOrNumeric($every = $input->getOption('every'));
        Assert::isArray($daysOfMonth = $input->getOption('days-of-month'));
        Assert::allNumeric($daysOfMonth);
        Assert::isArray($weeksOfMonth = $input->getOption('weeks-of-month'));
        Assert::allNumeric($weeksOfMonth);
        Assert::nullOrString($startDate = $input->getOption('start-date'));

        $response = $this->habitica->createTask(new Request(
            type: Type::from($type),
            text: $text,
            tags: $tags,
            difficulty: null !== $difficulty ? Difficulty::from($difficulty) : null,
            value: null !== $cost ? (float) $cost : null,
            notes: $notes,
            date: $date,
            checklist: array_map(fn (string $text) => new RequestChecklist($text), $checklist),
            collapseChecklist: $checklistCollapse,
            attribute: null !== $attribute ? Attribute::from($attribute) : null,
            frequency: null !== $frequency ? Frequency::from($frequency) : null,
            repeat: $this->getRepeat($input),
            everyX: null !== $every ? (int) $every : null,
            daysOfMonth: array_map(fn ($day) => (int) $day, $daysOfMonth),
            weeksOfMonth: array_map(fn ($week) => (int) $week, $weeksOfMonth),
            startDate: $startDate,
        ));

        $output->writeln($response->data->id);

        return self::SUCCESS;
    }

    private function getRepeat(InputInterface $input): ?Repeat
    {
        Assert::isArray($repeat = $input->getOption('repeat'));
        if (0 === count($repeat)) {
            return null;
        }

        Assert::allInArray($repeat, self::REPEAT);

        return new Repeat(
            su: in_array('su', $repeat, true),
            m: in_array('mo', $repeat, true),
            t: in_array('tu', $repeat, true),
            w: in_array('we', $repeat, true),
            th: in_array('th', $repeat, true),
            f: in_array('fr', $repeat, true),
            s: in_array('sa', $repeat, true),
        );
    }
}
