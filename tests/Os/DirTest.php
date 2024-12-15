<?php

declare(strict_types=1);

namespace App\Tests\Os;

use App\Os\Dir;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DirTest extends TestCase
{
    /**
     * @param array<string, string|false> $env
     */
    #[DataProvider('userCacheProvider')]
    public function testUserCache(string $os, array $env, string|RuntimeException $expected): void
    {
        if ($expected instanceof RuntimeException) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $dir = new Dir($os, $env);
        $result = $dir->userCache();

        self::assertSame($expected, $result);
    }

    /**
     * @return array<string, mixed>
     */
    public static function userCacheProvider(): array
    {
        return [
            'Windows' => [
                'Windows',
                ['LocalAppData' => 'C:\\Users\\User\\AppData\\Local'],
                'C:\\Users\\User\\AppData\\Local',
            ],
            'Windows missing LocalAppData env variable' => [
                'Windows',
                ['LocalAppData' => false],
                new RuntimeException('%LocalAppData% is not defined'),
            ],
            'Darwin' => [
                'Darwin',
                ['HOME' => '/Users/User'],
                '/Users/User/Library/Caches',
            ],
            'Darwin missing HOME env variable' => [
                'Darwin',
                ['HOME' => false],
                new RuntimeException('$HOME is not defined'),
            ],
            'Linux HOME' => [
                'Linux',
                ['HOME' => '/home/user'],
                '/home/user/.cache',
            ],
            'Linux XDG_CACHE_HOME' => [
                'Linux',
                ['XDG_CACHE_HOME' => '/tmp/.cache'],
                '/tmp/.cache',
            ],
            'Linux missing env variables' => [
                'Linux',
                ['HOME' => false, 'XDG_CACHE_DIR' => false],
                new RuntimeException('Neither $XDG_CACHE_HOME nor $HOME are defined'),
            ],
            'Unsupported OS' => [
                'UnknownOS',
                [],
                new RuntimeException('Unsupported OS: UnknownOS'),
            ],
        ];
    }
}
