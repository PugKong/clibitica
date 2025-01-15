<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class DeleteCommandTest extends AppTestCase
{
    #[DataProvider('successProvider')]
    public function testSuccess(string $id): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/delete.json');

        $actual = CommandTester::command('task:delete', ['id' => $id]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'DELETE /api/v3/tasks/60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e',
            'GET /api/v3/tasks/user',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'full id' => ['60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e'],
            'suggested id' => ['60d8-reward'],
        ];
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
                    22c2-habit	habit
                    bda4-daily	daily
                    e3e8-todo	todo
                    5949-default	default
                    60d8-reward	reward

                    EOF,
            ],
            '"re" filter' => [
                're',
                <<<'EOF'
                    60d8-reward	reward

                    EOF,
            ],
            '"e3e8614c" filter' => [
                'e3e8614c',
                <<<'EOF'
                    e3e8-todo	todo

                    EOF,
            ],
            '"22c2-h" filter' => [
                '22c2-h',
                <<<'EOF'
                    22c2-habit	habit

                    EOF,
            ],
        ];
    }
}
