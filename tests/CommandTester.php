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
    /**
     * @param array<string, mixed> $args
     */
    public static function command(string $command, array $args = []): CommandResult
    {
        return self::run(command: $command, args: $args);
    }

    /**
     * @param string[] $input
     */
    public static function completion(string $command, int $current, array $input = []): CommandResult
    {
        return self::run(
            command: '_complete',
            args: [
                '--shell' => 'zsh',
                '--api-version' => '1',
                '--input' => ['clibitica', $command, ...$input],
                '--current' => (string) $current,
            ],
        );
    }

    /**
     * @param array<string, mixed> $args
     */
    private static function run(string $command, array $args): CommandResult
    {
        $app = new App(Config::fromEnv());
        $buffer = new BufferedOutput();

        $code = $app->run(
            input: new ArrayInput(['command' => $command, ...$args]),
            output: $buffer,
            autoExit: false,
        );

        $output = implode(
            PHP_EOL,
            array_map(
                fn (string $line) => rtrim($line),
                explode(PHP_EOL, $buffer->fetch()),
            ),
        );

        return new CommandResult($code, $output);
    }
}
