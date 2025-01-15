<?php

declare(strict_types=1);

namespace App\Tests\Command\Tag;

use App\Tests\AppTestCase;
use App\Tests\CommandResult;
use App\Tests\CommandTester;
use PHPUnit\Framework\Attributes\DataProvider;

final class DeleteCommandTest extends AppTestCase
{
    #[DataProvider('successProvider')]
    public function testSuccess(string $id): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/delete.json');

        $actual = CommandTester::command('tag:delete', ['id' => $id]);

        self::assertEquals(new CommandResult(), $actual);

        $this->assertRequests([
            'DELETE /api/v3/tags/103dffda-0c51-49b8-bc25-6a387b5e28e8',
            'GET /api/v3/tags',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function successProvider(): array
    {
        return [
            'full id' => ['103dffda-0c51-49b8-bc25-6a387b5e28e8'],
            'suggested id' => ['103d-second'],
        ];
    }

    #[DataProvider('suggestIdProvider')]
    public function testSuggestId(string $input, string $output): void
    {
        $this->wireMock->addMappingFromFile(__DIR__.'/wiremock/list.json');

        $actual = CommandTester::completion('tag:delete', 2, [$input]);

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
            '"ir" filter' => [
                'ir',
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
            '"103d-s" filter' => [
                '103d-s',
                <<<'EOF'
                    103d-second	second

                    EOF,
            ],
        ];
    }
}
