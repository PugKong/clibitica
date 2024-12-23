<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;

final class ListCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::command('tag:list');

        $expected = new CommandResult(
            output: <<<'EOF'
                 ---------- --------
                  id         name
                 ---------- --------
                  b0f04338   first
                  103dffda   second
                 ---------- --------


                EOF,
        );

        self::assertEquals($expected, $actual);
    }
}
