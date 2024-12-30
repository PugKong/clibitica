<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\InputMapper\Exception\MapException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->do($input, $output);
        } catch (MapException $e) {
            $io = new SymfonyStyle($input, $output);
            $io->error($e->getMessage());

            return self::FAILURE;
        }
    }

    abstract protected function do(InputInterface $input, OutputInterface $output): int;
}
