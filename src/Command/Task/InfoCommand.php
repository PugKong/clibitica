<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Tag;
use App\Habitica\Task\Daily;
use App\Habitica\Task\Frequency;
use App\Habitica\Task\Habit;
use App\Habitica\Task\Reward;
use App\Habitica\Task\Task;
use App\Habitica\Task\Todo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;

#[AsCommand(name: 'task:info', description: 'Show task details')]
final class InfoCommand extends Command
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

        $this->mapper->configure($this, InfoInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, InfoInput::class);

        $task = $this->habitica->task($this->suggestions->reverseTaskId($data->task))->data;
        $tags = $this->makeTagsMap($this->habitica->listTags()->data);

        $list = [];
        $list[] = ['ID' => $task->id];
        $list[] = ['Type' => $task->type->value];

        if ($task instanceof Task) {
            $list[] = ['Attribute' => $task->attribute->value];
            $list[] = ['Difficulty' => $task->difficulty->value];
        }

        if ($task instanceof Habit) {
            $list[] = ['Frequency' => $task->frequency->value];
            $list[] = ['Ups / Downs' => $task->counterUp.' / '.$task->counterDown];
        }

        if ($task instanceof Daily) {
            $list[] = ['Start' => $task->startDate->format('Y-m-d')];
            $list[] = ['Frequency' => $task->frequency->value];

            if (1 !== $task->everyX) {
                $unit = match ($task->frequency) {
                    Frequency::DAILY => 'days',
                    Frequency::WEEKLY => 'weeks',
                    Frequency::MONTHLY => 'months',
                    Frequency::YEARLY => 'years',
                };
                $list[] = ['Every' => "$task->everyX $unit"];
            }

            if (Frequency::WEEKLY === $task->frequency) {
                $repeat = [
                    $task->repeat->su ? 'su' : '',
                    $task->repeat->m ? 'mo' : '',
                    $task->repeat->t ? 'tu' : '',
                    $task->repeat->w ? 'we' : '',
                    $task->repeat->th ? 'th' : '',
                    $task->repeat->f ? 'fr' : '',
                    $task->repeat->s ? 'sa' : '',
                ];

                $list[] = ['Repeat' => implode(', ', array_filter($repeat))];
            }

            if (count($task->daysOfMonth) > 0) {
                $list[] = ['Days of month' => implode(', ', $task->daysOfMonth)];
            }

            if (count($task->weeksOfMonth) > 0) {
                $list[] = ['Weeks of month' => implode(', ', $task->weeksOfMonth)];
            }

            $list[] = ['Streak' => $task->streak];
            $list[] = ['Done' => !$task->isDue || $task->completed ? 'true' : 'false'];
        }

        if ($task instanceof Todo && null !== $task->date) {
            $list[] = ['Due' => $task->date->format('Y-m-d')];
        }

        if ($task instanceof Reward) {
            $list[] = ['Cost' => $task->cost];
        }

        if (count($task->tags) > 0) {
            $list[] = ['Tags' => implode(', ', array_map(fn (string $tag) => $tags[$tag], $task->tags))];
        }

        $list[] = ['Text' => $task->text];

        if (($task instanceof Daily || $task instanceof Todo) && count($task->checklist) > 0) {
            $list[] = ['Checklist' => Util::formatChecklist($task)];
        }

        if ('' !== $task->notes) {
            $list[] = ['Notes' => $task->notes];
        }

        $list[] = ['Created' => $task->createdAt->format('Y-m-d H:i:s')];
        $list[] = ['Updated' => $task->updatedAt->format('Y-m-d H:i:s')];

        $io = new SymfonyStyle($input, $output);
        $io->definitionList(...$list);

        return self::SUCCESS;
    }

    /**
     * @param Tag\List\ResponseData[] $tags
     *
     * @return array<string, string>
     */
    private function makeTagsMap(array $tags): array
    {
        $result = [];
        foreach ($tags as $tag) {
            $result[$tag->id] = $tag->name;
        }

        return $result;
    }
}
