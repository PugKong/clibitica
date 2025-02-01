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
     * @param string[]             $fixtures
     * @param array<string, mixed> $args
     */
    #[DataProvider('successProvider')]
    public function testSuccess(array $fixtures, array $args, string $output): void
    {
        foreach ($fixtures as $fixture) {
            $this->wireMock->addMappingFromFile($fixture);
        }

        $actual = CommandTester::command('task:list', $args);

        self::assertEquals(new CommandResult(output: $output), $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'default' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                [],
                <<<'EOF'
                     ---------- ------- ------------ --------------- -------------------------
                      id         type    difficulty   tags            text
                     ---------- ------- ------------ --------------- -------------------------
                      22c23065   habit   trivial                      habit (up: 10; down: 5)
                      bda4bfdd   daily   easy                         daily (streak: 10)
                      e3e8614c   todo    medium       first, second   todo (due: 2024-12-28)
                      594980f9   todo    hard                         default
                     ---------- ------- ------------ --------------- -------------------------


                    EOF,
            ],
            'all' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--all' => true],
                <<<'EOF'
                     ---------- -------- ------------ --------------- -------------------------
                      id         type     difficulty   tags            text
                     ---------- -------- ------------ --------------- -------------------------
                      22c23065   habit    trivial                      habit (up: 10; down: 5)
                      bda4bfdd   daily    easy                         daily (streak: 10)
                      967371bc   daily    easy                         done (streak: 0)
                      6694402e   daily    easy                         not due (streak: 0)
                      e3e8614c   todo     medium       first, second   todo (due: 2024-12-28)
                      594980f9   todo     hard                         default
                      60d8c0ae   reward                                reward
                     ---------- -------- ------------ --------------- -------------------------


                    EOF,
            ],
            'habit' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--type' => 'habit'],
                <<<'EOF'
                     ---------- ------- ------------ -------------------------
                      id         type    difficulty   text
                     ---------- ------- ------------ -------------------------
                      22c23065   habit   trivial      habit (up: 10; down: 5)
                     ---------- ------- ------------ -------------------------


                    EOF,
            ],
            'daily' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--type' => 'daily'],
                <<<'EOF'
                     ---------- ------- ------------ --------------------
                      id         type    difficulty   text
                     ---------- ------- ------------ --------------------
                      bda4bfdd   daily   easy         daily (streak: 10)
                     ---------- ------- ------------ --------------------


                    EOF,
            ],
            'all daily' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--type' => 'daily', '--all' => true],
                <<<'EOF'
                     ---------- ------- ------------ ---------------------
                      id         type    difficulty   text
                     ---------- ------- ------------ ---------------------
                      bda4bfdd   daily   easy         daily (streak: 10)
                      967371bc   daily   easy         done (streak: 0)
                      6694402e   daily   easy         not due (streak: 0)
                     ---------- ------- ------------ ---------------------


                    EOF,
            ],
            'todo' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--type' => 'todo'],
                <<<'EOF'
                     ---------- ------ ------------ --------------- ------------------------
                      id         type   difficulty   tags            text
                     ---------- ------ ------------ --------------- ------------------------
                      e3e8614c   todo   medium       first, second   todo (due: 2024-12-28)
                      594980f9   todo   hard                         default
                     ---------- ------ ------------ --------------- ------------------------


                    EOF,
            ],
            'reward' => [
                [__DIR__.'/wiremock/list/list.json', __DIR__.'/wiremock/tag/list.json'],
                ['--type' => 'reward'],
                <<<'EOF'
                     ---------- -------- ------------ --------
                      id         type     difficulty   text
                     ---------- -------- ------------ --------
                      60d8c0ae   reward                reward
                     ---------- -------- ------------ --------


                    EOF,
            ],
            'checklist' => [
                [__DIR__.'/wiremock/list/checklist.json', __DIR__.'/wiremock/tag/list.json'],
                [],
                <<<'EOF'
                     ---------- ------- ------------ -------------------------------
                      id         type    difficulty   text
                     ---------- ------- ------------ -------------------------------
                      e2838a14   daily   easy         daily no collapse (streak: 0)
                                                      [ ] first
                                                      [ ] second
                      22967a5e   daily   easy         daily collapse (streak: 0)
                      33b24e11   todo    easy         todo collapse
                      4c7a20c4   todo    easy         todo no collapse
                                                      [ ] first
                                                      [ ] second
                     ---------- ------- ------------ -------------------------------


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
