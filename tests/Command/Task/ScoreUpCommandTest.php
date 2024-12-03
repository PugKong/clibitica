<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

class ScoreUpCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $tester = CommandTester::command('task:score:up', ['id' => '7f2d8f8d-36f2-48f1-8e85-6366b0ab4903']);

        $exitCode = $tester->run();

        self::assertSame('', $tester->output());
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $tester = CommandTester::completion('task:score:up', 2, [$input]);

        $exitCode = $tester->run();

        self::assertSame($output, $tester->output());
        self::assertSame(Command::SUCCESS, $exitCode);
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
        ];
    }
}
