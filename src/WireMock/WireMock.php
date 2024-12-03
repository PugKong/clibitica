<?php

declare(strict_types=1);

namespace App\WireMock;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final readonly class WireMock
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function reset(): void
    {
        $response = $this->client->request('POST', '__admin/reset');

        Assert::same($response->getStatusCode(), 200);
    }

    public function startRecording(): void
    {
        $response = $this->client->request('POST', '__admin/recordings/start', [
            'json' => [
                'targetBaseUrl' => 'https://habitica.com',
                'captureHeaders' => [
                    'Accept' => ['caseInsensitive' => true],
                    'Content-Type' => ['caseInsensitive' => true],
                    'X-Client' => ['caseInsensitive' => true],
                    'X-Api-Key' => ['caseInsensitive' => true],
                    'X-Api-User' => ['caseInsensitive' => true],
                ],
                'requestBodyPattern' => ['matcher' => 'equalToJso'],
                'repeatsAsScenarios' => false,
            ],
        ]);

        Assert::same($response->getStatusCode(), 200, $response->getContent(false));
    }

    public function stopRecording(): void
    {
        $response = $this->client->request('POST', '__admin/recordings/stop', []);

        Assert::same($response->getStatusCode(), 200, $response->getContent(false));
    }
}
