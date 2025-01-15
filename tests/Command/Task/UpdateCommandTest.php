<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class UpdateCommandTest extends AppTestCase
{
    /**
     * @param array<string, mixed> $args
     * @param string[]             $requests
     */
    #[DataProvider('successProvider')]
    public function testSuccess(string $fixture, array $args, array $requests): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/update/list.json');
        $this->wireMock->addMappingFromFile($fixture);

        $actual = CommandTester::command('task:update', $args);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests($requests);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'text' => [
                __DIR__.'/wiremock/update/text.json',
                ['id' => '5b5a273a-8a81-4750-88f7-a7698bd2a226', '--text' => 'My habit'],
                ['PUT /api/v3/tasks/5b5a273a-8a81-4750-88f7-a7698bd2a226', 'GET /api/v3/tasks/user'],
            ],
            'text by suggested id' => [
                __DIR__.'/wiremock/update/text.json',
                ['id' => '5b5a-habit', '--text' => 'My habit'],
                ['PUT /api/v3/tasks/5b5a273a-8a81-4750-88f7-a7698bd2a226', 'GET /api/v3/tasks/user'],
            ],
            'difficulty' => [
                __DIR__.'/wiremock/update/difficulty.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--difficulty' => 'hard'],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
            'cost' => [
                __DIR__.'/wiremock/update/cost.json',
                ['id' => '7f50f9ca-9151-4cf2-8704-b590dc50b56b', '--cost' => 20],
                ['PUT /api/v3/tasks/7f50f9ca-9151-4cf2-8704-b590dc50b56b', 'GET /api/v3/tasks/user'],
            ],
            'notes' => [
                __DIR__.'/wiremock/update/notes.json',
                ['id' => '7f50f9ca-9151-4cf2-8704-b590dc50b56b', '--notes' => 'The note'],
                ['PUT /api/v3/tasks/7f50f9ca-9151-4cf2-8704-b590dc50b56b', 'GET /api/v3/tasks/user'],
            ],
            'due' => [
                __DIR__.'/wiremock/update/due.json',
                ['id' => 'e401ceb6-2069-444f-8535-e71a19ebcca1', '--date' => '2025-01-24'],
                ['PUT /api/v3/tasks/e401ceb6-2069-444f-8535-e71a19ebcca1', 'GET /api/v3/tasks/user'],
            ],
            'checklist collapse' => [
                __DIR__.'/wiremock/update/checklist-collapse.json',
                ['id' => 'e401ceb6-2069-444f-8535-e71a19ebcca1', '--checklist-collapse' => true],
                ['PUT /api/v3/tasks/e401ceb6-2069-444f-8535-e71a19ebcca1', 'GET /api/v3/tasks/user'],
            ],
            'checklist no collapse' => [
                __DIR__.'/wiremock/update/checklist-no-collapse.json',
                ['id' => 'e401ceb6-2069-444f-8535-e71a19ebcca1', '--no-checklist-collapse' => true],
                ['PUT /api/v3/tasks/e401ceb6-2069-444f-8535-e71a19ebcca1', 'GET /api/v3/tasks/user'],
            ],
            'attribute' => [
                __DIR__.'/wiremock/update/attribute.json',
                ['id' => 'e401ceb6-2069-444f-8535-e71a19ebcca1', '--attribute' => 'int'],
                ['PUT /api/v3/tasks/e401ceb6-2069-444f-8535-e71a19ebcca1', 'GET /api/v3/tasks/user'],
            ],
            'repeat' => [
                __DIR__.'/wiremock/update/repeat.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--frequency' => 'weekly', '--repeat' => ['su']],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
            'every' => [
                __DIR__.'/wiremock/update/every.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--frequency' => 'weekly', '--every' => 2],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
            'days of month' => [
                __DIR__.'/wiremock/update/days-of-month.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--frequency' => 'monthly', '--days-of-month' => [2]],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
            'weeks of month' => [
                __DIR__.'/wiremock/update/weeks-of-month.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--frequency' => 'monthly', '--weeks-of-month' => [3]],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
            'start date' => [
                __DIR__.'/wiremock/update/start-date.json',
                ['id' => 'c3bed493-5561-4728-9a74-8cb183b349ce', '--start-date' => '2025-01-24'],
                ['PUT /api/v3/tasks/c3bed493-5561-4728-9a74-8cb183b349ce', 'GET /api/v3/tasks/user'],
            ],
        ];
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:update', 2, [$input]);

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
            '"reward" filter' => [
                'reward',
                <<<'EOF'
                    60d8-reward	reward

                    EOF,
            ],
            '"e3e861" filter' => [
                'e3e861',
                <<<'EOF'
                    e3e8-todo	todo

                    EOF,
            ],
            '"22c2-ha" filter' => [
                '22c2-ha',
                <<<'EOF'
                    22c2-habit	habit

                    EOF,
            ],
        ];
    }
}
