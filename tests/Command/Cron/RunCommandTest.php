<?php

declare(strict_types=1);

namespace App\Tests\Command\Cron;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;

final class RunCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/run.json');

        $actual = CommandTester::command('cron:run');

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests(['POST /api/v3/cron']);
    }
}
