<?php

declare(strict_types=1);

namespace App\Tests\Command\Task;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class InfoCommandTest extends AppTestCase
{
    #[DataProvider('successProvider')]
    public function testSuccess(string $fixture, string $id, CommandResult $expected): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/tag/list.json');
        $this->wireMock->addMappingFromFile($fixture);

        $actual = CommandTester::command('task:info', ['id' => $id]);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'habit' => [
                __DIR__.'/wiremock/info/habit.json',
                '596ba471-b87f-41b2-be45-c5977d30ea9f',
                new CommandResult(
                    output: <<<'EOF'
                         ------------- --------------------------------------
                          ID            596ba471-b87f-41b2-be45-c5977d30ea9f
                          Type          habit
                          Attribute     str
                          Difficulty    easy
                          Frequency     daily
                          Ups / Downs   10 / 5
                          Tags          first, second
                          Text          habit
                          Notes         some
                                        note
                          Created       2024-12-18 12:52:24
                          Updated       2024-12-18 12:52:24
                         ------------- --------------------------------------


                        EOF,
                ),
            ],
            'weekly' => [
                __DIR__.'/wiremock/info/weekly.json',
                '5f03fe51-384e-4476-ba01-64cc271efcbb',
                new CommandResult(
                    output: <<<'EOF'
                         ------------ --------------------------------------
                          ID           5f03fe51-384e-4476-ba01-64cc271efcbb
                          Type         daily
                          Attribute    int
                          Difficulty   medium
                          Start        2024-12-17
                          Frequency    weekly
                          Repeat       su, mo, tu, we, th, fr, sa
                          Streak       10
                          Done         false
                          Text         daily
                          Checklist    [x] one
                                       [ ] two
                          Created      2024-12-18 12:52:27
                          Updated      2024-12-18 12:52:27
                         ------------ --------------------------------------


                        EOF,
                ),
            ],
            'every 2 days' => [
                __DIR__.'/wiremock/info/every-2-days.json',
                '875850cf-22d5-47ef-9f94-61b63e7bca0f',
                new CommandResult(
                    output: <<<'EOF'
                         ------------ --------------------------------------
                          ID           875850cf-22d5-47ef-9f94-61b63e7bca0f
                          Type         daily
                          Attribute    str
                          Difficulty   trivial
                          Start        2024-12-18
                          Frequency    daily
                          Every        2 days
                          Streak       0
                          Done         false
                          Text         every 2 days
                          Created      2024-12-19 14:14:24
                          Updated      2024-12-19 14:14:38
                         ------------ --------------------------------------


                        EOF,
                ),
            ],
            'every 3 weeks' => [
                __DIR__.'/wiremock/info/every-3-weeks.json',
                'b788f6ac-4b86-4cec-95cc-23af146118e7',
                new CommandResult(
                    output: <<<'EOF'
                         ------------ --------------------------------------
                          ID           b788f6ac-4b86-4cec-95cc-23af146118e7
                          Type         daily
                          Attribute    str
                          Difficulty   easy
                          Start        2024-12-18
                          Frequency    weekly
                          Every        3 weeks
                          Repeat       mo, tu, we, th, fr
                          Streak       0
                          Done         false
                          Text         every 3 weeks
                          Created      2024-12-19 14:30:35
                          Updated      2024-12-19 14:31:13
                         ------------ --------------------------------------


                        EOF,
                ),
            ],
            'every 4 months' => [
                __DIR__.'/wiremock/info/every-4-months.json',
                'b7d4418d-032b-4525-97ef-7a8cc00978d9',
                new CommandResult(
                    output: <<<'EOF'
                         --------------- --------------------------------------
                          ID              b7d4418d-032b-4525-97ef-7a8cc00978d9
                          Type            daily
                          Attribute       str
                          Difficulty      easy
                          Start           2024-12-18
                          Frequency       monthly
                          Every           4 months
                          Days of month   19
                          Streak          0
                          Done            false
                          Text            every 4 months
                          Created         2024-12-19 14:30:40
                          Updated         2024-12-19 14:31:04
                         --------------- --------------------------------------


                        EOF,
                ),
            ],
            'every 5 years' => [
                __DIR__.'/wiremock/info/every-5-years.json',
                '541e4e86-b9af-4956-adef-46a84d53649b',
                new CommandResult(
                    output: <<<'EOF'
                         ------------ --------------------------------------
                          ID           541e4e86-b9af-4956-adef-46a84d53649b
                          Type         daily
                          Attribute    str
                          Difficulty   easy
                          Start        2024-12-18
                          Frequency    yearly
                          Every        5 years
                          Streak       0
                          Done         false
                          Text         every 5 years
                          Created      2024-12-19 14:30:44
                          Updated      2024-12-19 14:30:53
                         ------------ --------------------------------------


                        EOF,
                ),
            ],
            'days of month' => [
                __DIR__.'/wiremock/info/days-of-month.json',
                '7586a084-7422-43f0-b586-4273f4a9012a',
                new CommandResult(
                    output: <<<'EOF'
                         --------------- --------------------------------------
                          ID              7586a084-7422-43f0-b586-4273f4a9012a
                          Type            daily
                          Attribute       str
                          Difficulty      easy
                          Start           2024-12-18
                          Frequency       monthly
                          Days of month   19
                          Streak          0
                          Done            false
                          Text            days of month
                          Created         2024-12-19 14:15:07
                          Updated         2024-12-19 14:15:21
                         --------------- --------------------------------------


                        EOF,
                ),
            ],
            'weeks of month' => [
                __DIR__.'/wiremock/info/weeks-of-month.json',
                '2d0ddf10-69fa-4ce2-b38b-617bf5d6ce55',
                new CommandResult(
                    output: <<<'EOF'
                         ---------------- --------------------------------------
                          ID               2d0ddf10-69fa-4ce2-b38b-617bf5d6ce55
                          Type             daily
                          Attribute        str
                          Difficulty       easy
                          Start            2024-12-18
                          Frequency        monthly
                          Weeks of month   2
                          Streak           0
                          Done             false
                          Text             weeks of month
                          Created          2024-12-19 14:15:25
                          Updated          2024-12-19 14:15:43
                         ---------------- --------------------------------------


                        EOF,
                ),
            ],
            'todo' => [
                __DIR__.'/wiremock/info/todo.json',
                '09fd9cd1-448d-4c7a-9d20-46f8cd6f7fca',
                new CommandResult(
                    output: <<<'EOF'
                         ------------ --------------------------------------
                          ID           09fd9cd1-448d-4c7a-9d20-46f8cd6f7fca
                          Type         todo
                          Attribute    con
                          Difficulty   hard
                          Due          2024-12-28
                          Text         todo
                          Created      2024-12-18 12:52:32
                          Updated      2024-12-18 12:52:32
                         ------------ --------------------------------------


                        EOF,
                ),
            ],
            'reward' => [
                __DIR__.'/wiremock/info/reward.json',
                'f8091a56-7aaa-44e4-a998-6c31384a2842',
                new CommandResult(
                    output: <<<'EOF'
                         --------- --------------------------------------
                          ID        f8091a56-7aaa-44e4-a998-6c31384a2842
                          Type      reward
                          Cost      10
                          Tags      first
                          Text      reward
                          Notes     note
                          Created   2024-12-18 10:28:52
                          Updated   2024-12-18 12:52:38
                         --------- --------------------------------------


                        EOF,
                ),
            ],
        ];
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('task:info', 2, [$input]);

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
            '"bda4bfdd" filter' => [
                'bda4bfdd',
                <<<'EOF'
                    bda4bfdd-c38b-493b-8b2a-5dcad06034ba	daily

                    EOF,
            ],
        ];
    }
}
