<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Suggestions;
use App\Command\TaskDifficulty;
use App\Habitica\Habitica;
use App\Habitica\Task\Create\Request;
use App\Habitica\Task\Create\RequestChecklist;
use App\Habitica\Task\Type;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'task:create', description: 'Create a new task')]
final class CreateCommand extends Command
{
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

        $response = $this->habitica->createTask(new Request(
            type: Type::from($type),
            text: $text,
            tags: $tags,
            priority: null !== $difficulty ? TaskDifficulty::from($difficulty)->toPriority() : null,
            value: null !== $cost ? (float) $cost : null,
            notes: $notes,
            date: $date,
            checklist: array_map(fn (string $text) => new RequestChecklist($text), $checklist),
            collapseChecklist: $checklistCollapse,
        ));

        $output->writeln($response->data->id);

        return self::SUCCESS;
    }
}
