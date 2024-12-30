<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'task:score', description: 'Score task up or down')]
final class ScoreCommand extends Command
{
    public function __construct(private readonly Mapper $mappper, private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mappper->configure($this, ScoreInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mappper->map($input, ScoreInput::class);

        $this->habitica->scoreTask($data->task, $data->direction);

        return self::SUCCESS;
    }
}
