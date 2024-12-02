<?php

declare(strict_types=1);

namespace App\Tests;

use App\WireMock\WireMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

abstract class AppTestCase extends TestCase
{
    protected WireMock $wireMock;

    protected function setUp(): void
    {
        $this->wireMock = new WireMock(HttpClient::createForBaseUri(getenv('CLIBITICA_WIREMOCK_BASE_URL') ?: 'http://localhost:8080', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]));
        $this->wireMock->reset();
    }

    protected function tearDown(): void
    {
        unset($this->wireMock);
    }
}
