<?php

declare(strict_types=1);

namespace App\Tests;

final class AppTest extends AppTestCase
{
    public function testUsage(): void
    {
        $actual = CommandTester::command('');

        $expected = new CommandResult(
            output: <<<'EOF'
                clibitica 0.0.7

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
                  task:tag:add     Add a tag to a task
                  task:tag:delete  Remove a tag from a task
                 user
                  user:stats       Show user stats

                EOF,
        );

        self::assertEquals($expected, $actual);
    }
}
