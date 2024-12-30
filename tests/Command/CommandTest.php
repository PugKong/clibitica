<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\Command;
use App\Command\InputMapper\Attribute\Argument;
use App\Command\InputMapper\Exception\CastException;
use App\Command\InputMapper\Exception\MapException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\TypeInfo\Type;

use const PHP_EOL;

final class CommandTest extends TestCase
{
    public function testMapExceptionHandling(): void
    {
        $command = new class extends Command {
            protected function do(InputInterface $input, OutputInterface $output): int
            {
                throw new MapException(new Argument('id'), new CastException(Type::int(), 42));
            }
        };

        $result = $command->run(new ArrayInput([]), $output = new BufferedOutput());

        self::assertSame(
            <<<'EOF'

                 [ERROR] Argument id should be int, but 42 given


                EOF,
            implode(
                PHP_EOL,
                array_map(
                    fn (string $line) => rtrim($line),
                    explode(PHP_EOL, $output->fetch()),
                ),
            ),
        );
        self::assertSame(\Symfony\Component\Console\Command\Command::FAILURE, $result);
    }
}
