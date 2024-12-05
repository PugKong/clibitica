<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

final class ListCommandTest extends AppTestCase
{
    /**
     * @param array<string, mixed> $args
     */
    #[DataProvider('successProvider')]
    public function testSuccess(array $args, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');

        $tester = CommandTester::command('task:list', $args);

        $exitCode = $tester->run();

        self::assertSame($output, $tester->output());
        self::assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'all' => [
                [],
                <<<'EOF'
                     ---------- -------- ------------ ------------ --------------- ---------
                      id         type     difficulty   due          tags            text
                     ---------- -------- ------------ ------------ --------------- ---------
                      22c23065   habit    trivial                                   habit
                      bda4bfdd   daily    easy                                      daily
                      e3e8614c   todo     medium       2024-12-28   first, second   todo
                      594980f9   todo     hard                                      default
                      60d8c0ae   reward   easy                                      reward
                     ---------- -------- ------------ ------------ --------------- ---------


                    EOF,
            ],
            'habit' => [
                ['--type' => 'habit'],
                <<<'EOF'
                     ---------- ------- ------------ ----- ------ -------
                      id         type    difficulty   due   tags   text
                     ---------- ------- ------------ ----- ------ -------
                      22c23065   habit   trivial                   habit
                     ---------- ------- ------------ ----- ------ -------


                    EOF,
            ],
            'daily' => [
                ['--type' => 'daily'],
                <<<'EOF'
                     ---------- ------- ------------ ----- ------ -------
                      id         type    difficulty   due   tags   text
                     ---------- ------- ------------ ----- ------ -------
                      bda4bfdd   daily   easy                      daily
                     ---------- ------- ------------ ----- ------ -------


                    EOF,
            ],
            'todo' => [
                ['--type' => 'todo'],
                <<<'EOF'
                     ---------- ------ ------------ ------------ --------------- ---------
                      id         type   difficulty   due          tags            text
                     ---------- ------ ------------ ------------ --------------- ---------
                      e3e8614c   todo   medium       2024-12-28   first, second   todo
                      594980f9   todo   hard                                      default
                     ---------- ------ ------------ ------------ --------------- ---------


                    EOF,
            ],
            'reward' => [
                ['--type' => 'reward'],
                <<<'EOF'
                     ---------- -------- ------------ ----- ------ --------
                      id         type     difficulty   due   tags   text
                     ---------- -------- ------------ ----- ------ --------
                      60d8c0ae   reward   easy                      reward
                     ---------- -------- ------------ ----- ------ --------


                    EOF,
            ],
        ];
    }

    public function testSuggestType(): void
    {
        $tester = CommandTester::completion('task:list', 2, ['--type']);

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
}
