<?php

declare(strict_types=1);

namespace App\Command\WireMock\Recording;

use App\WireMock\WireMock;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wiremock:recording:stop', description: 'Stop recording')]
final class StopCommand extends Command
{
    public function __construct(private readonly WireMock $wireMock, bool $hidden)
    {
        parent::__construct();

        $this->setHidden($hidden);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->wireMock->stopRecording();

        return self::SUCCESS;
    }
}
