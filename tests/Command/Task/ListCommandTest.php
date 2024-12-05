<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

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

        $actual = CommandTester::command('task:list', $args);

        self::assertEquals(new CommandResult(output: $output), $actual);
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
        $actual = CommandTester::completion('task:list', 2, ['--type']);

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
}
