<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Task\Update\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'task:update', description: 'Update a task')]
final class UpdateCommand extends Command
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

        $this->mapper->configure($this, UpdateInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, UpdateInput::class);

        $this->habitica->updateTask(new Request(
            id: $this->suggestions->reverseTaskId($data->task),
            text: $data->text,
            difficulty: $data->difficulty,
            value: $data->cost,
            notes: $data->notes,
            date: $data->date,
            collapseChecklist: $data->checklistCollapse,
            attribute: $data->attribute,
            frequency: $data->frequency,
            repeat: Util::repeatArrayToObject($data->repeat),
            everyX: $data->everyX,
            daysOfMonth: $data->daysOfMonth,
            weeksOfMonth: $data->weeksOfMonth,
            startDate: $data->startDate,
        ));

        return self::SUCCESS;
    }
}
