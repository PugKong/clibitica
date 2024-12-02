<?php

declare(strict_types=1);

namespace App\Tests;

use App\App;
use App\Config;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

use const PHP_EOL;

final readonly class CommandTester
{
    private BufferedOutput $output;

    /**
     * @param array<string, mixed> $args
     */
    private function __construct(private string $command, private array $args)
    {
        $this->output = new BufferedOutput();
    }

    /**
     * @param array<string, mixed> $args
     */
    public static function command(string $command, array $args = []): self
    {
        return new self(command: $command, args: $args);
    }

    /**
     * @param string[] $input
     */
    public static function completion(string $command, int $current, array $input = []): self
    {
        return new self(
            command: '_complete',
            args: [
                '--shell' => 'zsh',
                '--api-version' => '1',
                '--input' => ['clibitica', $command, ...$input],
                '--current' => (string) $current,
            ],
        );
    }

    public function run(): int
    {
        $app = new App(Config::fromEnv());

        return $app->run(
            input: new ArrayInput(['command' => $this->command, ...$this->args]),
            output: $this->output,
            autoExit: false,
        );
    }

    public function output(): string
    {
        $output = explode(PHP_EOL, $this->output->fetch());
        foreach ($output as $i => $line) {
            $output[$i] = rtrim($line);
        }

        return implode(PHP_EOL, $output);
    }
}
