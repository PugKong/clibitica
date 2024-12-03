<?php

declare(strict_types=1);

namespace App\Command\WireMock;

use App\WireMock\WireMock;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wiremock:reset', description: 'Reset wiremock')]
final class ResetCommand extends Command
{
    public function __construct(private readonly WireMock $wireMock)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->wireMock->reset();

        return self::SUCCESS;
    }
}
