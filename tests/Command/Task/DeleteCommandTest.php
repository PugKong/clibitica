<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use App\WireMock\Request\List\ResponseRequest;
use PHPUnit\Framework\Attributes\DataProvider;

final class DeleteCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/delete.json');

        $actual = CommandTester::command('task:delete', ['id' => $id = '60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e']);

        self::assertEquals(new CommandResult(), $actual);
        self::assertSame(
            [['method' => 'DELETE', 'url' => "/api/v3/tasks/$id"]],
            array_map(
                fn (ResponseRequest $request) => [
                    'method' => $request->request->method,
                    'url' => $request->request->url,
                ],
                $this->wireMock->listRequests()->requests,
            ),
        );
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:delete', 2, [$input]);

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
            '"e" filter' => [
                'e',
                <<<'EOF'
                    594980f9-f9da-4087-9bea-d7c48bb9ced1	default
                    60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e	reward

                    EOF,
            ],
        ];
    }
}
