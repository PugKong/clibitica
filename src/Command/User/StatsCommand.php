<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Command\Command;
use App\Habitica\Habitica;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

#[AsCommand(name: 'user:stats', description: 'Show user stats')]
final class StatsCommand extends Command
{
    public function __construct(private readonly Habitica $habitica)
    {
        parent::__construct();
    }

    protected function do(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->habitica->getUser();
        $stats = $user->data->stats;

        $output->writeln("$stats->class, $stats->lvl lvl");
        $output->writeln('');

        $this->bar($output, 'HP ', 'green', $stats->hp, $stats->maxHealth);
        $this->bar($output, 'MP ', 'blue', $stats->mp, $stats->maxMP);
        $this->bar($output, 'EXP', 'yellow', $stats->exp, $stats->toNextLevel);
        $output->writeln('');

        $output->writeln(implode(' ', [
            "str: <fg=green>$stats->str</>",
            "con: <fg=red>$stats->con</>",
            "int: <fg=blue>$stats->int</>",
            "per: <fg=yellow>$stats->per</>",
        ]));
        $output->writeln('');

        $output->writeln(sprintf('gold: <fg=yellow>%.2f</>', $stats->gp));

        return self::SUCCESS;
    }

    private function bar(OutputInterface $output, string $label, string $color, int $current, int $max): void
    {
        $len = 28;
        $progress = (int) ($len * $current / $max);
        $bar = str_repeat('=', $progress).str_repeat('-', $len - $progress);

        $output->writeln("<fg=$color>$label $bar $current / $max</>");
    }
}
