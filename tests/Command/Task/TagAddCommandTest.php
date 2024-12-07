<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use App\WireMock\Request\List\ResponseRequest;

class TagAddCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/add.json');

        $actual = CommandTester::command('task:tag:add', [
            'task' => $task = '0e2f79f8-26e6-49da-bd63-f83326179dd3',
            'tag' => $tag = '8a43dcd2-ed0a-44c9-80e0-cf8dd122f631',
        ]);

        self::assertEquals(new CommandResult(), $actual);
        self::assertSame(
            [['method' => 'POST', 'url' => "/api/v3/tasks/$task/tags/$tag"]],
            array_map(
                fn (ResponseRequest $request) => [
                    'method' => $request->request->method,
                    'url' => $request->request->url,
                ],
                $this->wireMock->listRequests()->requests,
            ),
        );
    }

    public function testSuggestTaskId(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:tag:add', 2);

        $expected = new CommandResult(
            output: <<<'EOF'
                22c23065-84c1-4f4f-aede-2509a692eeb5	habit
                bda4bfdd-c38b-493b-8b2a-5dcad06034ba	daily
                e3e8614c-9758-4b78-b154-067586e7a06f	todo
                594980f9-f9da-4087-9bea-d7c48bb9ced1	default
                60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e	reward

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    public function testSuggestTagId(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');

        $actual = CommandTester::completion('task:tag:add', 3, ['']);

        $expected = new CommandResult(
            output: <<<'EOF'
                b0f04338-8666-4db8-8d0b-faa375748cf7	first
                103dffda-0c51-49b8-bc25-6a387b5e28e8	second

                EOF,
        );
        self::assertEquals($expected, $actual);
    }
}
