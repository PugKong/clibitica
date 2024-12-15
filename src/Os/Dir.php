<?php

declare(strict_types=1);

namespace App\Os;

use RuntimeException;

use function sprintf;

use const PHP_OS_FAMILY;

final readonly class Dir
{
    /**
     * @param array<string, string|false> $env
     */
    public function __construct(private string $os = PHP_OS_FAMILY, private array $env = [])
    {
    }

    public function userCache(): string
    {
        return match ($this->os) {
            'Windows' => $this->userCacheWindows(),
            'Darwin' => $this->userCacheDarwin(),
            'Linux' => $this->userCacheLinux(),
            default => throw new RuntimeException(sprintf('Unsupported OS: %s', $this->os)),
        };
    }

    private function userCacheWindows(): string
    {
        $dir = $this->getEnv('LocalAppData');
        if (!$dir) {
            throw new RuntimeException('%LocalAppData% is not defined');
        }

        return $dir;
    }

    private function userCacheDarwin(): string
    {
        $dir = $this->getEnv('HOME');
        if (!$dir) {
            throw new RuntimeException('$HOME is not defined');
        }

        return $dir.'/Library/Caches';
    }

    private function userCacheLinux(): string
    {
        $dir = $this->getEnv('XDG_CACHE_HOME');
        if (false !== $dir) {
            return $dir;
        }

        $dir = $this->getEnv('HOME');
        if (!$dir) {
            throw new RuntimeException('Neither $XDG_CACHE_HOME nor $HOME are defined');
        }

        return $dir.'/.cache';
    }

    private function getEnv(string $name): string|false
    {
        return $this->env[$name] ?? getenv($name);
    }
}
