<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use Symfony\Component\Console\Command\Command;

final class ListCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $tester = CommandTester::command('tag:list');

        $exitCode = $tester->run();

        self::assertSame(
            <<<'EOF'
                 -------------------------------------- --------
                  id                                     name
                 -------------------------------------- --------
                  b0f04338-8666-4db8-8d0b-faa375748cf7   first
                  103dffda-0c51-49b8-bc25-6a387b5e28e8   second
                 -------------------------------------- --------


                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }
}
