<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use App\WireMock\Request\List\ResponseRequest;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScoreCommandTest extends AppTestCase
{
    public function testUp(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/up.json');

        $actual = CommandTester::command('task:score', [
            'direction' => 'up',
            'id' => $id = '7f2d8f8d-36f2-48f1-8e85-6366b0ab4903',
        ]);

        self::assertEquals(new CommandResult(), $actual);
        self::assertSame(
            [['method' => 'POST', 'url' => "/api/v3/tasks/$id/score/up"]],
            array_map(
                fn (ResponseRequest $request) => [
                    'method' => $request->request->method,
                    'url' => $request->request->url,
                ],
                $this->wireMock->listRequests()->requests,
            ),
        );
    }

    public function testDown(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/down.json');

        $actual = CommandTester::command('task:score', [
            'direction' => 'down',
            'id' => $id = '7f2d8f8d-36f2-48f1-8e85-6366b0ab4903',
        ]);

        self::assertEquals(new CommandResult(), $actual);
        self::assertSame(
            [['method' => 'POST', 'url' => "/api/v3/tasks/$id/score/down"]],
            array_map(
                fn (ResponseRequest $request) => [
                    'method' => $request->request->method,
                    'url' => $request->request->url,
                ],
                $this->wireMock->listRequests()->requests,
            ),
        );
    }

    public function testSuggestDirection(): void
    {
        $actual = CommandTester::completion('task:score', 2);

        $expected = new CommandResult(
            output: <<<'EOF'
                up
                down

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:score', 3, ['up', $input]);

        self::assertEquals(new CommandResult(output: $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function suggestIdProvider(): array
    {
        return [
            'no filter' => [
                '',
                <<<'EOF'
                    22c23065-84c1-4f4f-aede-2509a692eeb5	habit
                    bda4bfdd-c38b-493b-8b2a-5dcad06034ba	daily
                    e3e8614c-9758-4b78-b154-067586e7a06f	todo
                    594980f9-f9da-4087-9bea-d7c48bb9ced1	default
                    60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e	reward

                    EOF,
            ],
            '"def" filter' => [
                'def',
                <<<'EOF'
                    594980f9-f9da-4087-9bea-d7c48bb9ced1	default

                    EOF,
            ],
            '"60d8c0ae" filter' => [
                '60d8c0ae',
                <<<'EOF'
                    60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e	reward

                    EOF,
            ],
        ];
    }
}
