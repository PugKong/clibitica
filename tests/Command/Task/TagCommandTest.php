<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class TagCommandTest extends AppTestCase
{
    #[DataProvider('idProvider')]
    public function testAdd(string $task, string $tag): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/tasks.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/add.json');

        $actual = CommandTester::command('task:tag', [
            'action' => 'add',
            'task' => $task,
            'tag' => $tag,
        ]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'POST /api/v3/tasks/0e2f79f8-26e6-49da-bd63-f83326179dd3/tags/b0f04338-8666-4db8-8d0b-faa375748cf7',
            'GET /api/v3/tags',
            'GET /api/v3/tasks/user',
        ]);
    }

    #[DataProvider('idProvider')]
    public function testDelete(string $task, string $tag): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/tasks.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/delete.json');

        $actual = CommandTester::command('task:tag', [
            'action' => 'delete',
            'task' => $task,
            'tag' => $tag,
        ]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'DELETE /api/v3/tasks/0e2f79f8-26e6-49da-bd63-f83326179dd3/tags/b0f04338-8666-4db8-8d0b-faa375748cf7',
            'GET /api/v3/tags',
            'GET /api/v3/tasks/user',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function idProvider(): array
    {
        return [
            'full id' => ['0e2f79f8-26e6-49da-bd63-f83326179dd3', 'b0f04338-8666-4db8-8d0b-faa375748cf7'],
            'suggested id' => ['0e2f-some-text', 'b0f0-first'],
        ];
    }

    public function testSuggestAction(): void
    {
        $actual = CommandTester::completion('task:tag', 2);

        $expected = new CommandResult(
            output: <<<'EOF'
                add
                delete

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    public function testSuggestTaskId(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:tag', 3, ['']);

        $expected = new CommandResult(
            output: <<<'EOF'
                22c2-habit	habit
                bda4-daily	daily
                e3e8-todo	todo
                5949-default	default
                60d8-reward	reward

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    public function testSuggestTagId(): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');

        $actual = CommandTester::completion('task:tag', 4, ['', '']);

        $expected = new CommandResult(
            output: <<<'EOF'
                b0f0-first	first
                103d-second	second

                EOF,
        );
        self::assertEquals($expected, $actual);
    }
}
