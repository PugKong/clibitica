<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'task:tag', description: 'Manage tags for a task')]
final class TagCommand extends Command
{
    public function __construct(private readonly Mapper $mapper, private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, TagInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, TagInput::class);

        if (TagAction::ADD === $data->action) {
            $this->habitica->addTagToTask($data->task, $data->tag);
        } else {
            $this->habitica->deleteTagFromTask($data->task, $data->tag);
        }

        return self::SUCCESS;
    }
}
