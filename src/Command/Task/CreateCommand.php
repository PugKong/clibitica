<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Task\Create\Request;
use App\Habitica\Task\Create\RequestChecklist;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'task:create', description: 'Create a new task', aliases: ['task:add'])]
final class CreateCommand extends Command
{
    public function __construct(
        private readonly Mapper $mapper,
        private readonly Habitica $habitica,
        private readonly Suggestions $suggestions,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, CreateInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, CreateInput::class);

        $response = $this->habitica->createTask(new Request(
            type: $data->type,
            text: $data->text,
            tags: array_map(fn ($tag) => $this->suggestions->reverseTagId($tag), $data->tags),
            difficulty: $data->difficulty,
            value: $data->cost,
            notes: $data->notes,
            date: $data->date,
            checklist: array_map(fn (string $text) => new RequestChecklist($text), $data->checklist),
            collapseChecklist: $data->checklistCollapse,
            attribute: $data->attribute,
            frequency: $data->frequency,
            repeat: Util::repeatArrayToObject($data->repeat),
            everyX: $data->everyX,
            daysOfMonth: $data->daysOfMonth,
            weeksOfMonth: $data->weeksOfMonth,
            startDate: $data->startDate,
        ));

        $output->writeln($response->data->id);

        return self::SUCCESS;
    }
}
