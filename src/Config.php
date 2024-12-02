<?php

declare(strict_types=1);

namespace App;

final readonly class Config
{
    public function __construct(
        public bool $dev,
        public string $habiticaBaseUrl,
        public string $habiticaApiKey,
        public string $habiticaApiUser,
        public string $wireMockBaseUrl,
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            dev: (bool) getenv('CLIBITICA_DEV'),
            habiticaBaseUrl: getenv('CLIBITICA_BASE_URL') ?: 'https://habitica.com',
            habiticaApiKey: getenv('CLIBITICA_API_KEY') ?: '',
            habiticaApiUser: getenv('CLIBITICA_API_USER') ?: '',
            wireMockBaseUrl: getenv('CLIBITICA_WIREMOCK_BASE_URL') ?: 'http://localhost:8080',
        );
    }
}
