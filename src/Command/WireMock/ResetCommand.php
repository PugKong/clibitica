<?php

declare(strict_types=1);

namespace App\Command\WireMock;

use App\Command\Command;
use App\WireMock\WireMock;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wiremock:reset', description: 'Reset wiremock')]
final class ResetCommand extends Command
{
    public function __construct(private readonly WireMock $wireMock, bool $hidden)
    {
        parent::__construct();

        $this->setHidden($hidden);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $this->wireMock->reset();

        return self::SUCCESS;
    }
}
