<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateCommandTest extends AppTestCase
{
    #[DataProvider('successProvider')]
    public function testSuccess(string $name, string $id, string $command = 'tag:create'): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/create/first.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/create/second.json');

        $actual = CommandTester::command($command, ['name' => $name]);

        $expected = new CommandResult(
            output: <<<EOF
                {$id}

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
            'first' => ['first', 'b0f04338-8666-4db8-8d0b-faa375748cf7'],
            'first (via tag:add alias)' => ['first', 'b0f04338-8666-4db8-8d0b-faa375748cf7', 'tag:add'],
            'second' => ['second', '103dffda-0c51-49b8-bc25-6a387b5e28e8'],
        ];
    }
}
