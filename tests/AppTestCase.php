<?php

declare(strict_types=1);

namespace App\Tests;

use App\WireMock\Request\List\ResponseRequest;
use App\WireMock\WireMock;
use PHPUnit\Framework\TestCase;

abstract class AppTestCase extends TestCase
{
    protected WireMock $wireMock;

    protected function setUp(): void
    {
        $baseUri = getenv('CLIBITICA_WIREMOCK_BASE_URL') ?: 'http://localhost:8080';

        $this->wireMock = WireMock::create($baseUri);
        $this->wireMock->reset();
    }

    protected function tearDown(): void
    {
        unset($this->wireMock);
    }

    /**
     * @param string[] $requests
     */
    public function assertRequests(array $requests): void
    {
        self::assertSame(
            $requests,
            array_map(
                fn (ResponseRequest $request) => $request->request->method.' '.$request->request->url,
                $this->wireMock->listRequests()->requests,
            ),
        );
    }
}
