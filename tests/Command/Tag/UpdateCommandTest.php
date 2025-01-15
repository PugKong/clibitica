<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class UpdateCommandTest extends AppTestCase
{
    /**
     * @param array<string, mixed> $args
     */
    #[DataProvider('successProvider')]
    public function testSuccess(string $fixture, array $args): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');
        $this->wireMock->addMappingFromFile($fixture);

        $actual = CommandTester::command('tag:update', $args);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'PUT /api/v3/tags/b0f04338-8666-4db8-8d0b-faa375748cf7',
            'GET /api/v3/tags',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'name' => [
                __DIR__.'/wiremock/update/name.json',
                ['id' => 'b0f04338-8666-4db8-8d0b-faa375748cf7', '--name' => 'Third'],
            ],
            'name by suggested id' => [
                __DIR__.'/wiremock/update/name.json',
                ['id' => 'b0f0-first', '--name' => 'Third'],
            ],
        ];
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('tag:update', 2, [$input]);

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
                    b0f0-first	first
                    103d-second	second

                    EOF,
            ],
            '"first" filter' => [
                'first',
                <<<'EOF'
                    b0f0-first	first

                    EOF,
            ],
            '"b0f04338" filter' => [
                'b0f04338',
                <<<'EOF'
                    b0f0-first	first

                    EOF,
            ],
            '"103d" filter' => [
                '103d',
                <<<'EOF'
                    103d-second	second

                    EOF,
            ],
        ];
    }
}
