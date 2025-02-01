<?php

declare(strict_types=1);

namespace App\Tests;

final class AppTest extends AppTestCase
{
    public function testUsage(): void
    {
        $actual = CommandTester::command('list');

        $expected = new CommandResult(
            output: <<<'EOF'
                clibitica 0.1.1

                Usage:
                  command [options] [arguments]

                Options:
                  -h, --help            Display help for the given command. When no command is given display help for the task:list command
                      --silent          Do not output any message
                  -q, --quiet           Only errors are displayed. All other output is suppressed
                  -V, --version         Display this application version
                      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
                  -n, --no-interaction  Do not ask any interactive question
                  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

                Available commands:
                  completion      Dump the shell completion script
                  help            Display help for a command
                  list            List commands
                 cron
                  cron:run        This causes cron to run, it will immediately apply damage for incomplete due Dailies
                 tag
                  tag:create      Create a new tag
                  tag:delete      Delete a tag
                  tag:list        Get tags
                  tag:update      Update a tag
                 task
                  task:checklist  Manage checklist for a task
                  task:create     [task:add] Create a new task
                  task:delete     Delete a task
                  task:info       Show task details
                  task:list       List tasks
                  task:score      Score task up or down
                  task:tag        Manage tags for a task
                  task:update     Update a task
                 user
                  user:stats      Show user stats

                EOF,
        );

        self::assertEquals($expected, $actual);
    }
}
