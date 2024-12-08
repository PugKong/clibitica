<?php

declare(strict_types=1);

namespace App\Tests\Command\User;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;

class StatsCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/user.json');

        $actual = CommandTester::command('user:stats');

        $expected = new CommandResult(
            output: <<<'EOF'
                warrior, 27 lvl

                HP  ============================ 50 / 50
                MP  ============================ 84 / 84
                EXP ==================---------- 385 / 590

                str: 17 con: 10 int: 0 per: 0

                EOF,
        );

        self::assertEquals($expected, $actual);
    }
}
