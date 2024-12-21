<?php

declare(strict_types=1);

namespace App\Tests\Command\InputMapper;

use App\Command\InputMapper\Mapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @template T of object
 */
final class FakeCommand extends Command
{
    /**
     * @var T
     */
    public mixed $result;

    /**
     * @param class-string<T> $class
     */
    public function __construct(private readonly Mapper $mapper, private readonly string $class)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->mapper->configure($this, $this->class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->result = $this->mapper->map($input, $this->class);

        return self::SUCCESS;
    }
}
