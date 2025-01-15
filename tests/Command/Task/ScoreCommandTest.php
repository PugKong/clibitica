<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScoreCommandTest extends AppTestCase
{
    #[DataProvider('idProvider')]
    public function testUp(string $id): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/up.json');

        $actual = CommandTester::command('task:score', [
            'direction' => 'up',
            'id' => $id,
        ]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'POST /api/v3/tasks/7f2d8f8d-36f2-48f1-8e85-6366b0ab4903/score/up',
            'GET /api/v3/tasks/user',
        ]);
    }

    #[DataProvider('idProvider')]
    public function testDown(string $id): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/score/down.json');

        $actual = CommandTester::command('task:score', [
            'direction' => 'down',
            'id' => $id,
        ]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'POST /api/v3/tasks/7f2d8f8d-36f2-48f1-8e85-6366b0ab4903/score/down',
            'GET /api/v3/tasks/user',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function idProvider(): array
    {
        return [
            'full id' => ['7f2d8f8d-36f2-48f1-8e85-6366b0ab4903'],
            'suggested id' => ['7f2d-habit'],
        ];
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
                    22c2-habit	habit
                    bda4-daily	daily
                    e3e8-todo	todo
                    5949-default	default
                    60d8-reward	reward

                    EOF,
            ],
            '"def" filter' => [
                'def',
                <<<'EOF'
                    5949-default	default

                    EOF,
            ],
            '"60d8c0ae" filter' => [
                '60d8c0ae',
                <<<'EOF'
                    60d8-reward	reward

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
