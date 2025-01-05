<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\Command;
use App\Command\InputMapper\Mapper;
use App\Command\Suggestions;
use App\Habitica\Habitica;
use App\Habitica\Tag\Update\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'tag:update', description: 'Update a tag')]
final class UpdateCommand extends Command
{
    public function __construct(
        private readonly Mapper $mapper,
        private readonly Habitica $habitica,
        private readonly Suggestions $suggestions,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, UpdateInput::class);
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->mapper->map($input, UpdateInput::class);

        $this->habitica->updateTag(new Request(
            id: $this->suggestions->reverseTagId($data->tag),
            name: $data->name,
        ));

        return self::SUCCESS;
    }
}
