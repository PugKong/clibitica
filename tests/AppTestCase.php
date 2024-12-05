<?php

declare(strict_types=1);

namespace App\Tests;

use App\Http\Http;
use App\WireMock\WireMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

abstract class AppTestCase extends TestCase
{
    protected WireMock $wireMock;

    protected function setUp(): void
    {
        $baseUri = getenv('CLIBITICA_WIREMOCK_BASE_URL') ?: 'http://localhost:8080';
        $client = HttpClient::createForBaseUri($baseUri, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $serializer = new Serializer(
            normalizers: [],
            encoders: [new JsonEncoder()],
        );

        $this->wireMock = new WireMock(new Http($client, $serializer));
        $this->wireMock->reset();
    }

    protected function tearDown(): void
    {
        unset($this->wireMock);
    }
}
