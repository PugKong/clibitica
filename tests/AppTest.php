<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\Console\Command\Command;

final class AppTest extends AppTestCase
{
    public function testUsage(): void
    {
        $tester = CommandTester::command('');

        $exitCode = $tester->run();

        self::assertSame(
            <<<'EOF'
                clibitica 0.0.2

                Usage:
                  command [options] [arguments]

                Options:
                  -h, --help            Display help for the given command. When no command is given display help for the list command
                      --silent          Do not output any message
                  -q, --quiet           Only errors are displayed. All other output is suppressed
                  -V, --version         Display this application version
                      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
                  -n, --no-interaction  Do not ask any interactive question
                  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

                Available commands:
                  completion       Dump the shell completion script
                  help             Display help for a command
                  list             List commands
                 tag
                  tag:create       Create a new tag
                  tag:delete       Delete a tag
                  tag:list         Get tags
                 task
                  task:create      Create a new task
                  task:delete      Delete a task
                  task:list        List tasks
                  task:score:down  Score task down
                  task:score:up    Score task up

                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }
}