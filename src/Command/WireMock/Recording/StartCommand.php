<?php

declare(strict_types=1);

namespace App\Command\WireMock\Recording;

use App\Command\Command;
use App\WireMock\WireMock;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wiremock:recording:start', description: 'Start recording')]
final class StartCommand extends Command
{
    public function __construct(private readonly WireMock $wireMock, bool $hidden)
    {
        parent::__construct();

        $this->setHidden($hidden);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $this->wireMock->startRecording();

        return self::SUCCESS;
    }
}
