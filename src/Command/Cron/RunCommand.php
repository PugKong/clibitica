<?php

declare(strict_types=1);

namespace App\Command\Cron;

use App\Command\Command;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cron:run', description: 'This causes cron to run, it will immediately apply damage for incomplete due Dailies')]
final class RunCommand extends Command
{
    public function __construct(private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $this->habitica->runCron();

        return self::SUCCESS;
    }
}
