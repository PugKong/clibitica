<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\InputMapper\Mapper;
use App\Habitica\Habitica;
use App\Habitica\Tag\Create\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'tag:create', description: 'Create a new tag')]
final class CreateCommand extends Command
{
    public function __construct(private readonly Mapper $mapper, private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, CreateInput::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, CreateInput::class);

        $response = $this->habitica->createTag(new Request(name: $data->name));

        $output->writeln($response->data->id);

        return self::SUCCESS;
    }
}
