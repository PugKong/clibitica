<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Mapper;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'task:delete', description: 'Delete a task')]
final class DeleteCommand extends Command
{
    public function __construct(private readonly Mapper $mapper, private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, DeleteInput::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, DeleteInput::class);

        $this->habitica->deleteTask($data->task);

        return self::SUCCESS;
    }
}
