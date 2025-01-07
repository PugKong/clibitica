<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Command\Task\ChecklistAction;
use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use App\WireMock\Request\List\ResponseRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

use const PHP_EOL;

final class ChecklistCommandTest extends AppTestCase
{
    /**
     * @param string[]             $fixtures
     * @param array<string, mixed> $args
     * @param string[]             $requests
     */
    #[DataProvider('successProvider')]
    public function testSuccess(array $fixtures, array $args, array $requests): void
    {
        foreach ($fixtures as $fixture) {
            $this->wireMock->addMappingFromFile($fixture);
        }

        $actual = CommandTester::command('task:checklist', $args);

        self::assertEquals(new CommandResult(), $actual);
        self::assertSame(
            $requests,
            array_map(
                fn (ResponseRequest $request) => $request->request->method.' '.$request->request->url,
                $this->wireMock->listRequests()->requests,
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'add' => [
                [
                    __DIR__.'/wiremock/checklist/add/tasks.json',
                    __DIR__.'/wiremock/checklist/add/add.json',
                ],
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'add',
                    'item text or id' => 'third',
                ],
                [
                    'POST /api/v3/tasks/8d9ed79a-ff65-4894-9da5-291e313e776b/checklist',
                    'GET /api/v3/tasks/user',
                ],
            ],
            'delete' => [
                [
                    __DIR__.'/wiremock/checklist/delete/tasks.json',
                    __DIR__.'/wiremock/checklist/delete/delete.json',
                ],
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'delete',
                    'item text or id' => 'd3b2-third',
                ],
                [
                    'DELETE /api/v3/tasks/8d9ed79a-ff65-4894-9da5-291e313e776b/checklist/d3b2d518-2f78-4fcb-a2ee-3cf4e60b1a97',
                    'GET /api/v3/tasks/user',
                ],
            ],
            'delete (full item id)' => [
                [
                    __DIR__.'/wiremock/checklist/delete/tasks.json',
                    __DIR__.'/wiremock/checklist/delete/delete.json',
                ],
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'delete',
                    'item text or id' => 'd3b2d518-2f78-4fcb-a2ee-3cf4e60b1a97',
                ],
                [
                    'DELETE /api/v3/tasks/8d9ed79a-ff65-4894-9da5-291e313e776b/checklist/d3b2d518-2f78-4fcb-a2ee-3cf4e60b1a97',
                    'GET /api/v3/tasks/user',
                ],
            ],
            'check' => [
                [
                    __DIR__.'/wiremock/checklist/toggle/check_tasks.json',
                    __DIR__.'/wiremock/checklist/toggle/check.json',
                ],
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'toggle',
                    'item text or id' => 'ec97ebb3-cf1a-45be-84db-e6e4797a8f0d',
                ],
                [
                    'PUT /api/v3/tasks/8d9ed79a-ff65-4894-9da5-291e313e776b/checklist/ec97ebb3-cf1a-45be-84db-e6e4797a8f0d',
                    'GET /api/v3/tasks/user',
                ],
            ],
            'uncheck' => [
                [
                    __DIR__.'/wiremock/checklist/toggle/uncheck_tasks.json',
                    __DIR__.'/wiremock/checklist/toggle/uncheck.json',
                ],
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'toggle',
                    'item text or id' => 'ec97ebb3-cf1a-45be-84db-e6e4797a8f0d',
                ],
                [
                    'PUT /api/v3/tasks/8d9ed79a-ff65-4894-9da5-291e313e776b/checklist/ec97ebb3-cf1a-45be-84db-e6e4797a8f0d',
                    'GET /api/v3/tasks/user',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     */
    #[DataProvider('errorProvider')]
    public function testError(string $fixture, array $args, string $output): void
    {
        $this->wireMock->addMappingFromFile($fixture);

        $actual = CommandTester::command('task:checklist', $args);

        self::assertEquals(new CommandResult(Command::FAILURE, $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function errorProvider(): array
    {
        return [
            'task not found' => [
                __DIR__.'/wiremock/checklist/toggle/check_tasks.json',
                [
                    'id' => 'some task',
                    'action' => 'toggle',
                    'item text or id' => 'ec97ebb3-cf1a-45be-84db-e6e4797a8f0d',
                ],
                <<<'EOF'

                     [ERROR] Task "some task" not found


                    EOF,
            ],
            'checklist item not found' => [
                __DIR__.'/wiremock/checklist/toggle/check_tasks.json',
                [
                    'id' => '8d9ed79a-ff65-4894-9da5-291e313e776b',
                    'action' => 'toggle',
                    'item text or id' => 'some item',
                ],
                <<<'EOF'

                     [ERROR] Checklist item "some item" not found


                    EOF,
            ],
        ];
    }

    #[DataProvider('suggestTaskIdProvider')]
    public function testSuggestTaskId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/checklist/suggestion/tasks.json');

        $actual = CommandTester::completion('task:checklist', 1, [$input]);

        self::assertEquals(new CommandResult(output: $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function suggestTaskIdProvider(): array
    {
        return [
            'all' => [
                '',
                <<<'EOF'
                    38e5-daily	daily
                    8d9e-todo	todo

                    EOF,
            ],
            'filtered by text' => [
                'daily',
                <<<'EOF'
                    38e5-daily	daily

                    EOF,
            ],
            'filtered by suggested id' => [
                '8d9e-todo',
                <<<'EOF'
                    8d9e-todo	todo

                    EOF,
            ],
        ];
    }

    public function testSuggestAction(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/checklist/suggestion/tasks.json');

        $actual = CommandTester::completion('task:checklist', 2, ['8d9e-todo', '']);

        self::assertEquals(
            new CommandResult(
                output: <<<'EOF'
                    add
                    delete
                    toggle

                    EOF,
            ),
            $actual,
        );
    }

    /**
     * @param string[] $input
     */
    #[DataProvider('suggestChecklistItemIdProvider')]
    public function testSuggestChecklistItemId(array $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/checklist/suggestion/tasks.json');

        $actual = CommandTester::completion('task:checklist', 3, $input);

        self::assertEquals(new CommandResult(output: $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function suggestChecklistItemIdProvider(): array
    {
        return [
            'add' => [['8d9e-todo', ChecklistAction::ADD->value, ''], PHP_EOL],
            'delete all' => [
                ['8d9e-todo', ChecklistAction::DELETE->value, ''],
                <<<'EOF'
                    bea2-first	first
                    ec97-second	second

                    EOF,
            ],
            'delete filtered' => [
                ['8d9e-todo', ChecklistAction::DELETE->value, 'first'],
                <<<'EOF'
                    bea2-first	first

                    EOF,
            ],
            'toggle all' => [
                ['8d9e-todo', ChecklistAction::TOGGLE->value, ''],
                <<<'EOF'
                    bea2-first	first
                    ec97-second	second

                    EOF,
            ],
            'toggle filtered' => [
                ['8d9e-todo', ChecklistAction::TOGGLE->value, 'ec97-second'],
                <<<'EOF'
                    ec97-second	second

                    EOF,
            ],
        ];
    }
}
