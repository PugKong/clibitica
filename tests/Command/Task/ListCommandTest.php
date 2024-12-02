<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use Symfony\Component\Console\Command\Command;

final class ListCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $tester = CommandTester::command('task:list');

        $exitCode = $tester->run();

        self::assertSame(
            <<<'EOF'
                 -------------------------------------- -------- ---------
                  id                                     type     text
                 -------------------------------------- -------- ---------
                  22c23065-84c1-4f4f-aede-2509a692eeb5   habit    habit
                  bda4bfdd-c38b-493b-8b2a-5dcad06034ba   daily    daily
                  e3e8614c-9758-4b78-b154-067586e7a06f   todo     todo
                  594980f9-f9da-4087-9bea-d7c48bb9ced1   todo     default
                  60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e   reward   reward
                 -------------------------------------- -------- ---------


                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }
}
