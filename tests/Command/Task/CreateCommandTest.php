<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

final class CreateCommandTest extends AppTestCase
{
    /**
     * @param array<string, mixed> $args
     */
    #[DataProvider('successProvider')]
    public function testSuccess(array $args, string $uuid): void
    {
        $tester = CommandTester::command('task:create', $args);

        $exitCode = $tester->run();

        self::assertSame(
            <<<EOF
                {$uuid}

                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'default' => [['text' => 'default'], '594980f9-f9da-4087-9bea-d7c48bb9ced1'],
            'todo' => [['text' => 'todo', '--type' => 'todo'], 'e3e8614c-9758-4b78-b154-067586e7a06f'],
            'habit' => [['text' => 'habit', '--type' => 'habit'], '22c23065-84c1-4f4f-aede-2509a692eeb5'],
            'daily' => [['text' => 'daily', '--type' => 'daily'], '66ea5485-ae6f-491a-8d59-5da3a12d58bd'],
            'reward' => [['text' => 'reward', '--type' => 'reward'], '60d8c0ae-07d2-44f1-8d48-4bdf57e6f59e'],
            'tagged' => [
                ['text' => 'tagged', '--tags' => ['b0f04338-8666-4db8-8d0b-faa375748cf7']],
                'cc5d3501-0e09-4769-ba9f-a74fc9b0dc33',
            ],
            'trivial' => [['text' => 'trivial', '--difficulty' => 'trivial'], 'fef70fc7-07fa-4bb7-980b-a2f452419054'],
            'easy' => [['text' => 'easy', '--difficulty' => 'easy'], 'b91d4342-53a4-4d0b-ade6-8ab470cc96ee'],
            'medium' => [['text' => 'medium', '--difficulty' => 'medium'], 'baa0096a-add9-4aad-bc89-a581c0d7357c'],
            'hard' => [['text' => 'hard', '--difficulty' => 'hard'], '6bbc90db-b5fb-4b73-af09-2254aa90600c'],
            'cost 66.66' => [
                ['text' => 'cost 66.66', '--type' => 'reward', '--cost' => '66.66'],
                'b0cbc707-15e2-4965-ba68-62d91c31f5eb',
            ],
            'notes' => [['text' => 'notes', '--notes' => 'notes'], 'c2525a7c-2b95-4959-85de-60743e82b1a6'],
            'date' => [['text' => 'date', '--date' => '2024-12-28'], 'ab9e4cfd-cd95-4261-9e28-f71591768235'],
            'checklist' => [
                ['text' => 'checklist', '--checklist' => ['one', 'two']],
                'a3485b54-1f85-4ec6-aa1a-48d557c3ffc7',
            ],
            'checklist collapse' => [
                ['text' => 'collapse', '--checklist' => ['collapse'], '--checklist-collapse' => true],
                '2cbc0e47-08fb-439a-83de-3be9042a48a4',
            ],
            'checklist no collapse' => [
                ['text' => 'no-collapse', '--checklist' => ['no-collapse'], '--no-checklist-collapse' => true],
                'cd9f7af4-66bb-4af6-b40b-389cfdf62097',
            ],
        ];
    }

    public function testSuggestType(): void
    {
        $tester = CommandTester::completion('task:create', 2, ['--type']);

        $exitCode = $tester->run();

        self::assertSame(
            <<<'EOF'
                habit
                daily
                todo
                reward

                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    public function testSuggestDifficulty(): void
    {
        $tester = CommandTester::completion('task:create', 2, ['--difficulty']);

        $exitCode = $tester->run();

        self::assertSame(
            <<<'EOF'
                trivial
                easy
                medium
                hard

                EOF,
            $tester->output(),
        );
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    #[DataProvider('suggestTagIdProvider')]
    public function testSuggestTagId(string $input, string $output): void
    {
        $tester = CommandTester::completion('task:create', 3, ['--tags', $input]);

        $exitCode = $tester->run();

        self::assertSame($output, $tester->output());
        self::assertSame(Command::SUCCESS, $exitCode);
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
                'con',
                <<<'EOF'
                    103dffda-0c51-49b8-bc25-6a387b5e28e8	second

                    EOF,
            ],
        ];
    }
}
