<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Habitica\Habitica;
use App\Habitica\Tag\Create\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'tag:create', description: 'Create a new tag')]
final class CreateCommand extends Command
{
    public function __construct(private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            name: 'name',
            mode: InputArgument::REQUIRED,
            description: 'The name of the tag to be added',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::string($name = $input->getArgument('name'));

        $response = $this->habitica->createTag(new Request(name: $name));

        $output->writeln($response->data->id);

        return self::SUCCESS;
    }
}
