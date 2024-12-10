<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateCommandTest extends AppTestCase
{
    /**
     * @param array<string, mixed> $args
     */
    #[DataProvider('successProvider')]
    public function testSuccess(string $fixture, array $args, string $uuid): void
    {
        $this->wireMock->addMappingFromFile($fixture);

        $actual = CommandTester::command('task:create', $args);

        $expected = new CommandResult(
            output: <<<EOF
                {$uuid}

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'default' => [
                __DIR__.'/wiremock/create/default.json',
                ['text' => 'default'],
                '594980f9-f9da-4087-9bea-d7c48bb9ced1',
            ],

            'todo' => [
                __DIR__.'/wiremock/create/type/todo.json',
                ['text' => 'todo', '--type' => 'todo'],
                'e3e8614c-9758-4b78-b154-067586e7a06f',
            ],
            'habit' => [
                __DIR__.'/wiremock/create/type/habit.json',
                ['text' => 'habit', '--type' => 'habit'],
                '22c23065-84c1-4f4f-aede-2509a692eeb5',
            ],
            'daily' => [
                __DIR__.'/wiremock/create/type/daily.json',
                ['text' => 'daily', '--type' => 'daily'],
                '66ea5485-ae6f-491a-8d59-5da3a12d58bd',
            ],
            'reward' => [
                __DIR__.'/wiremock/create/type/reward.json',
                ['text' => 'reward', '--type' => 'reward'],
                '60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e',
            ],

            'tagged' => [
                __DIR__.'/wiremock/create/tagged.json',
                ['text' => 'tagged', '--tags' => ['b0f04338-8666-4db8-8d0b-faa375748cf7']],
                'cc5d3501-0e09-4769-ba9f-a74fc9b0dc33',
            ],

            'trivial' => [
                __DIR__.'/wiremock/create/difficulty/trivial.json',
                ['text' => 'trivial', '--difficulty' => 'trivial'],
                'fef70fc7-07fa-4bb7-980b-a2f452419054',
            ],
            'easy' => [
                __DIR__.'/wiremock/create/difficulty/easy.json',
                ['text' => 'easy', '--difficulty' => 'easy'],
                'b91d4342-53a4-4d0b-ade6-8ab470cc96ee',
            ],
            'medium' => [
                __DIR__.'/wiremock/create/difficulty/medium.json',
                ['text' => 'medium', '--difficulty' => 'medium'],
                'baa0096a-add9-4aad-bc89-a581c0d7357c',
            ],
            'hard' => [
                __DIR__.'/wiremock/create/difficulty/hard.json',
                ['text' => 'hard', '--difficulty' => 'hard'],
                '6bbc90db-b5fb-4b73-af09-2254aa90600c',
            ],

            'cost 66.66' => [
                __DIR__.'/wiremock/create/cost.json',
                ['text' => 'cost 66.66', '--type' => 'reward', '--cost' => '66.66'],
                'b0cbc707-15e2-4965-ba68-62d91c31f5eb',
            ],

            'notes' => [
                __DIR__.'/wiremock/create/notes.json',
                ['text' => 'notes', '--notes' => 'notes'],
                'c2525a7c-2b95-4959-85de-60743e82b1a6',
            ],

            'date' => [
                __DIR__.'/wiremock/create/date.json',
                ['text' => 'date', '--date' => '2024-12-28'],
                'ab9e4cfd-cd95-4261-9e28-f71591768235',
            ],

            'checklist' => [
                __DIR__.'/wiremock/create/checklist/checklist.json',
                ['text' => 'checklist', '--checklist' => ['one', 'two']],
                'a3485b54-1f85-4ec6-aa1a-48d557c3ffc7',
            ],
            'checklist collapse' => [
                __DIR__.'/wiremock/create/checklist/collapse.json',
                ['text' => 'collapse', '--checklist' => ['collapse'], '--checklist-collapse' => true],
                '2cbc0e47-08fb-439a-83de-3be9042a48a4',
            ],
            'checklist no collapse' => [
                __DIR__.'/wiremock/create/checklist/nocollapse.json',
                ['text' => 'no-collapse', '--checklist' => ['no-collapse'], '--no-checklist-collapse' => true],
                'cd9f7af4-66bb-4af6-b40b-389cfdf62097',
            ],

            'con' => [
                __DIR__.'/wiremock/create/attribute/const.json',
                ['text' => 'con', '--attribute' => 'con'],
                '93e5d93e-97e1-49bf-ac81-6542d479b5b4',
            ],
            'int' => [
                __DIR__.'/wiremock/create/attribute/int.json',
                ['text' => 'int', '--attribute' => 'int'],
                '370ce498-65c0-466f-845e-6e3b91314f47',
            ],
            'per' => [
                __DIR__.'/wiremock/create/attribute/per.json',
                ['text' => 'per', '--attribute' => 'per'],
                'c1e094ff-383e-4ea2-993d-12713d2f1656',
            ],
            'str' => [
                __DIR__.'/wiremock/create/attribute/str.json',
                ['text' => 'str', '--attribute' => 'str'],
                'a99b7f21-ef88-433f-9ac7-3eefffb1b041',
            ],
        ];
    }

    public function testSuggestType(): void
    {
        $actual = CommandTester::completion('task:create', 2, ['--type']);

        $expected = new CommandResult(
            output: <<<'EOF'
                habit
                daily
                todo
                reward

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    public function testSuggestDifficulty(): void
    {
        $actual = CommandTester::completion('task:create', 2, ['--difficulty']);

        $expected = new CommandResult(
            output: <<<'EOF'
                trivial
                easy
                medium
                hard

                EOF,
        );

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('suggestTagIdProvider')]
    public function testSuggestTagId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');

        $actual = CommandTester::completion('task:create', 3, ['--tags', $input]);

        self::assertEquals(new CommandResult(output: $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function suggestTagIdProvider(): array
    {
        return [
            'no filter' => [
                '',
                <<<'EOF'
                    b0f04338-8666-4db8-8d0b-faa375748cf7	first
                    103dffda-0c51-49b8-bc25-6a387b5e28e8	second

                    EOF,
            ],
            '"con" filter' => [
                'second',
                <<<'EOF'
                    103dffda-0c51-49b8-bc25-6a387b5e28e8	second

                    EOF,
            ],
            '"b0f04338" filter' => [
                'b0f04338',
                <<<'EOF'
                    b0f04338-8666-4db8-8d0b-faa375748cf7	first

                    EOF,
            ],
        ];
    }

    public function testSuggestAttribute(): void
    {
        $actual = CommandTester::completion('task:create', 3, ['--attribute']);

        $expected = new CommandResult(
            output: <<<'EOF'
                str
                int
                per
                con

                EOF,
        );

        self::assertEquals($expected, $actual);
    }
}
