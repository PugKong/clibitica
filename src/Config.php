<?php

declare(strict_types=1);

namespace App;

use App\Os\Dir;

use const PHP_OS_FAMILY;

final readonly class Config
{
    public function __construct(
        public bool $dev,
        public string $habiticaBaseUrl,
        public string $habiticaApiKey,
        public string $habiticaApiUser,
        public string $wireMockBaseUrl,
        public string $cacheDirectory,
    ) {
    }

    /**
     * @param array<string, string|false> $env
     */
    public static function fromEnv(string $os = PHP_OS_FAMILY, array $env = []): self
    {
        $dir = new Dir($os, $env);

        return new self(
            dev: (bool) getenv('CLIBITICA_DEV'),
            habiticaBaseUrl: getenv('CLIBITICA_BASE_URL') ?: 'https://habitica.com',
            habiticaApiKey: getenv('CLIBITICA_API_KEY') ?: '',
            habiticaApiUser: getenv('CLIBITICA_API_USER') ?: '',
            wireMockBaseUrl: getenv('CLIBITICA_WIREMOCK_BASE_URL') ?: 'http://localhost:8080',
            cacheDirectory: $dir->userCache().'/clibitica',
        );
    }
}
