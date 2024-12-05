<?php

declare(strict_types=1);

namespace App\Tests\Http;

use App\Http\Http;
use App\Http\StatusCodeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\SerializerInterface;

final class HttpTest extends TestCase
{
    public function testStatusCodeValidation(): void
    {
        $this->expectException(StatusCodeException::class);
        $this->expectExceptionMessage(<<<'EOF'
            400 return for POST https://example.com/, expected 200
            response body: the body
            EOF);

        $response = new MockResponse('the body', ['http_code' => 400]);
        $client = new MockHttpClient($response);
        $http = new Http($client, $this->createStub(SerializerInterface::class));

        $http
            ->post('https://example.com')
            ->fetch()
        ;
    }
}
