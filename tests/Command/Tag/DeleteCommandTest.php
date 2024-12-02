<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

final class DeleteCommandTest extends AppTestCase
{
    public function testSuccess(): void
    {
        $tester = CommandTester::command('tag:delete', ['id' => '103dffda-0c51-49b8-bc25-6a387b5e28e8']);

        $exitCode = $tester->run();

        self::assertSame('', $tester->output());
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $tester = CommandTester::completion('tag:delete', 2, [$input]);

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
                    b0f04338-8666-4db8-8d0b-faa375748cf7	first
                    103dffda-0c51-49b8-bc25-6a387b5e28e8	second

                    EOF,
            ],
            '"ir" filter' => [
                'ir',
                <<<'EOF'
                    b0f04338-8666-4db8-8d0b-faa375748cf7	first

                    EOF,
            ],
        ];
    }
}
