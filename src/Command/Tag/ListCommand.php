<?php

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\Command;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'tag:list', description: 'Get tags')]
final class ListCommand extends Command
{
    public function __construct(private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->habitica->listTags();

        $rows = [];
        foreach ($response->data as $tag) {
            $rows[] = [substr($tag->id, 0, 8), $tag->name];
        }

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['id', 'name'],
            $rows,
        );

        return self::SUCCESS;
    }
}
