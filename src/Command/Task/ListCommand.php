<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('task:list', description: 'List tasks')]
final class ListCommand extends Command
{
    public function __construct(private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->habitica->listTasks();

        $rows = [];
        foreach ($response->data as $task) {
            $rows[] = [$task->id, $task->type->value, $task->text];
        }

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['id', 'type', 'text'],
            $rows,
        );

        return self::SUCCESS;
    }
}
